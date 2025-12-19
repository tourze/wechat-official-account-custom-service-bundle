<?php

namespace WechatOfficialAccountCustomServiceBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountBundle\Repository\AccountRepository;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountCustomServiceBundle\Entity\KfAccount;
use WechatOfficialAccountCustomServiceBundle\Enum\KfAccountStatus;
use WechatOfficialAccountCustomServiceBundle\Repository\KfAccountRepository;
use WechatOfficialAccountCustomServiceBundle\Request\GetKfAccountListRequest;

#[AsCommand(
    name: self::NAME,
    description: '同步微信公众号客服账号',
)]
final class SyncKfAccountCommand extends Command
{
    public const NAME = 'wechat-official-account:custom-service:sync-account-list';

    public function __construct(
        private readonly OfficialAccountClient $client,
        private readonly EntityManagerInterface $entityManager,
        private readonly AccountRepository $accountRepository,
        private readonly KfAccountRepository $kfAccountRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('account-id', null, InputOption::VALUE_OPTIONAL, '公众号ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // 获取需要同步的公众号列表
        $accounts = [];
        $accountId = $input->getOption('account-id');
        if (null !== $accountId) {
            if (!is_string($accountId) && !is_numeric($accountId)) {
                $io->error('account-id 选项必须是字符串或数字');

                return Command::FAILURE;
            }
            $accountIdString = (string) $accountId;
            $account = $this->accountRepository->find($accountIdString);
            if (null === $account) {
                $io->error(sprintf('公众号 %s 不存在', $accountIdString));

                return Command::FAILURE;
            }
            $accounts[] = $account;
        } else {
            $accounts = $this->accountRepository->findAll();
        }

        foreach ($accounts as $account) {
            $io->section(sprintf('正在同步公众号 %s 的客服账号', $account->getName()));

            try {
                $this->syncAccount($account, $io);
            } catch (\Throwable $e) {
                $io->error(sprintf('同步失败：%s', $e->getMessage()));
                continue;
            }
        }

        return Command::SUCCESS;
    }

    private function syncAccount(Account $account, SymfonyStyle $io): void
    {
        $remoteKfList = $this->getRemoteKfList($account, $io);
        $localKfMap = $this->getLocalKfMap($account);

        if ([] === $remoteKfList) {
            // 远程没有客服账号，标记所有本地账号为删除
            $this->markDeletedKfAccounts($localKfMap, $io);
        } else {
            // 有远程客服账号，进行正常同步
            $remainingKfMap = $this->syncRemoteKfAccounts($remoteKfList, $localKfMap, $account, $io);
            $this->markDeletedKfAccounts($remainingKfMap, $io);
        }

        $this->entityManager->flush();
    }

    /**
     * @return array<mixed>
     */
    private function getRemoteKfList(Account $account, SymfonyStyle $io): array
    {
        $request = new GetKfAccountListRequest();
        $request->setAccount($account);
        /** @var array<string, mixed> $response */
        $response = $this->client->request($request);

        /** @var array<mixed> $remoteKfList */
        $remoteKfList = $response['kf_list'] ?? [];
        if ([] === $remoteKfList) {
            $io->info('没有客服账号');
        }

        return $remoteKfList;
    }

    /**
     * @return array<string, KfAccount>
     */
    private function getLocalKfMap(Account $account): array
    {
        /** @var KfAccount[] $localKfList */
        $localKfList = $this->kfAccountRepository->findBy(['account' => $account]);
        /** @var array<string, KfAccount> $localKfMap */
        $localKfMap = [];
        foreach ($localKfList as $kf) {
            $localKfMap[$kf->getKfAccount()] = $kf;
        }

        return $localKfMap;
    }

    /**
     * @param array<mixed> $remoteKfList
     * @param array<string, KfAccount> $localKfMap
     * @return array<string, KfAccount>
     */
    private function syncRemoteKfAccounts(array $remoteKfList, array $localKfMap, Account $account, SymfonyStyle $io): array
    {
        foreach ($remoteKfList as $remoteKf) {
            $localKfMap = $this->processRemoteKfAccount($remoteKf, $localKfMap, $account, $io);
        }

        return $localKfMap;
    }

    /**
     * @param array<string, KfAccount> $localKfMap
     * @return array<string, KfAccount>
     */
    private function processRemoteKfAccount(mixed $remoteKf, array $localKfMap, Account $account, SymfonyStyle $io): array
    {
        if (!is_array($remoteKf)) {
            return $localKfMap;
        }
        /** @var array<string, mixed> $remoteKf */
        if (!isset($remoteKf['kf_account'], $remoteKf['kf_nick'], $remoteKf['kf_id'])) {
            return $localKfMap;
        }
        $kfAccount = is_string($remoteKf['kf_account']) ? $remoteKf['kf_account'] : '';
        $nickname = is_string($remoteKf['kf_nick']) ? $remoteKf['kf_nick'] : '';
        $kfId = is_string($remoteKf['kf_id']) ? $remoteKf['kf_id'] : '';
        $avatar = isset($remoteKf['kf_headimgurl']) && is_string($remoteKf['kf_headimgurl']) ? $remoteKf['kf_headimgurl'] : null;

        if (isset($localKfMap[$kfAccount])) {
            /** @var KfAccount $existingKf */
            $existingKf = $localKfMap[$kfAccount];
            $this->updateExistingKfAccount($existingKf, $nickname, $kfId, $avatar, $io);
        } else {
            $this->createNewKfAccount($account, $kfAccount, $nickname, $kfId, $avatar, $io);
        }

        unset($localKfMap[$kfAccount]);

        return $localKfMap;
    }

    private function updateExistingKfAccount(KfAccount $kf, string $nickname, string $kfId, ?string $avatar, SymfonyStyle $io): void
    {
        $kf->setSyncing(true);
        $kf->setNickname($nickname);
        $kf->setKfId($kfId);
        if (null !== $avatar) {
            $kf->setAvatar($avatar);
        }
        $io->info(sprintf('更新客服账号：%s (ID: %s)', $kf->getKfAccount(), $kfId));
    }

    private function createNewKfAccount(Account $account, string $kfAccount, string $nickname, string $kfId, ?string $avatar, SymfonyStyle $io): void
    {
        $kf = new KfAccount();
        $kf->setSyncing(true);
        $kf->setAccount($account);
        $kf->setKfAccount($kfAccount);
        $kf->setNickname($nickname);
        $kf->setKfId($kfId);
        $kf->setStatus(KfAccountStatus::ENABLED);
        if (null !== $avatar) {
            $kf->setAvatar($avatar);
        }
        $this->entityManager->persist($kf);
        $io->info(sprintf('新增客服账号：%s (ID: %s)', $kfAccount, $kfId));
    }

    /**
     * @param array<string, KfAccount> $localKfMap
     */
    private function markDeletedKfAccounts(array $localKfMap, SymfonyStyle $io): void
    {
        foreach ($localKfMap as $kf) {
            $kf->setStatus(KfAccountStatus::DELETED);
            $io->info(sprintf('标记删除客服账号：%s (ID: %s)', $kf->getKfAccount(), $kf->getKfId()));
        }
    }
}
