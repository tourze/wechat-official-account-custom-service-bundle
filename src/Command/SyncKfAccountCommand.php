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
    name: 'wechat-official-account:custom-service:sync-account-list',
    description: '同步微信公众号客服账号',
)]
class SyncKfAccountCommand extends Command
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
        if ($accountId !== null) {
            $account = $this->accountRepository->find($accountId);
            if (!$account) {
                $io->error(sprintf('公众号 %s 不存在', $accountId));

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
        // 获取远程客服列表
        $request = new GetKfAccountListRequest();
        $request->setAccount($account);
        $response = $this->client->request($request);

        $remoteKfList = $response['kf_list'] ?? [];
        if (empty($remoteKfList)) {
            $io->info('没有客服账号');

            return;
        }

        // 获取本地客服列表
        $localKfList = $this->kfAccountRepository->findBy(['account' => $account]);
        $localKfMap = [];
        foreach ($localKfList as $kf) {
            $localKfMap[$kf->getKfAccount()] = $kf;
        }

        // 同步客服账号
        foreach ($remoteKfList as $remoteKf) {
            $kfAccount = $remoteKf['kf_account'];
            $nickname = $remoteKf['kf_nick'];
            $kfId = $remoteKf['kf_id'];
            $avatar = $remoteKf['kf_headimgurl'] ?? null;

            if (isset($localKfMap[$kfAccount])) {
                // 更新已存在的客服账号
                $kf = $localKfMap[$kfAccount];
                $kf->setSyncing(true);
                $kf->setNickname($nickname);
                $kf->setKfId($kfId);
                if ($avatar !== null) {
                    $kf->setAvatar($avatar);
                }
                $io->info(sprintf('更新客服账号：%s (ID: %s)', $kfAccount, $kfId));
            } else {
                // 创建新的客服账号
                $kf = new KfAccount();
                $kf->setSyncing(true);
                $kf->setAccount($account);
                $kf->setKfAccount($kfAccount);
                $kf->setNickname($nickname);
                $kf->setKfId($kfId);
                $kf->setStatus(KfAccountStatus::ENABLED);
                if ($avatar !== null) {
                    $kf->setAvatar($avatar);
                }
                $this->entityManager->persist($kf);
                $io->info(sprintf('新增客服账号：%s (ID: %s)', $kfAccount, $kfId));
            }

            unset($localKfMap[$kfAccount]);
        }

        // 处理已删除的客服账号
        foreach ($localKfMap as $kf) {
            $kf->setStatus(KfAccountStatus::DELETED);
            $io->info(sprintf('标记删除客服账号：%s (ID: %s)', $kf->getKfAccount(), $kf->getKfId()));
        }

        $this->entityManager->flush();
    }
}
