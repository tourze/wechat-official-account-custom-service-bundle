<?php

namespace WechatOfficialAccountCustomServiceBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountCustomServiceBundle\Entity\KfAccount;
use WechatOfficialAccountCustomServiceBundle\Enum\KfAccountStatus;
use WechatOfficialAccountCustomServiceBundle\Request\AddKfAccountRequest;
use WechatOfficialAccountCustomServiceBundle\Request\DeleteKfAccountRequest;
use WechatOfficialAccountCustomServiceBundle\Request\UpdateKfAccountRequest;
use WechatOfficialAccountCustomServiceBundle\Request\UploadKfAccountHeadimgRequest;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: KfAccount::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: KfAccount::class)]
#[AsEntityListener(event: Events::preRemove, method: 'preRemove', entity: KfAccount::class)]
#[WithMonologChannel(channel: 'wechat_official_account_custom_service')]
final class KfAccountListener
{
    public function __construct(
        private readonly OfficialAccountClient $client,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 新增客服账号，同步到远程
     */
    public function postPersist(KfAccount $account): void
    {
        if ($account->isSyncing()) {
            return;
        }
        if (KfAccountStatus::ENABLED !== $account->getStatus()) {
            return;
        }

        $request = new AddKfAccountRequest();
        $request->setAccount($account->getAccount());
        $request->setKfAccount($account->getKfAccount());
        $request->setNickname($account->getNickname());
        $request->setPassword($account->getPassword());

        try {
            $this->client->request($request);

            // 如果有头像，上传头像
            if (null !== $account->getAvatar()) {
                $this->uploadAvatar($account);
            }
        } catch (\Throwable $exception) {
            $this->logger->error('Failed to sync KfAccount', [
                'account' => $account,
                'exception' => $exception,
            ]);
        }
    }

    /**
     * 更新客服账号，同步到远程
     */
    public function postUpdate(KfAccount $account): void
    {
        if ($account->isSyncing()) {
            return;
        }
        if (KfAccountStatus::ENABLED !== $account->getStatus()) {
            return;
        }

        try {
            // 更新基本信息
            $request = new UpdateKfAccountRequest();
            $request->setAccount($account->getAccount());
            $request->setKfAccount($account->getKfAccount());
            $request->setNickname($account->getNickname());
            $password = $account->getPassword();
            if (null !== $password) {
                $request->setPassword($password);
            }

            $this->client->request($request);

            $this->uploadAvatar($account);
        } catch (\Throwable $exception) {
            $this->logger->error('Failed to update KfAccount', [
                'account' => $account,
                'exception' => $exception,
            ]);
        }
    }

    /**
     * 删除客服账号前，同步到远程
     */
    public function preRemove(KfAccount $account): void
    {
        if ($account->isSyncing()) {
            return;
        }

        try {
            $request = new DeleteKfAccountRequest();
            $request->setAccount($account->getAccount());
            $request->setKfAccount($account->getKfAccount());

            $this->client->request($request);
        } catch (\Throwable $exception) {
            $this->logger->error('Failed to delete KfAccount', [
                'account' => $account,
                'exception' => $exception,
            ]);
        }
    }

    /**
     * 上传客服头像
     */
    private function uploadAvatar(KfAccount $account): void
    {
        $avatar = $account->getAvatar();
        if (null === $avatar) {
            return;
        }

        try {
            $request = new UploadKfAccountHeadimgRequest();
            $request->setAccount($account->getAccount());
            $request->setKfAccount($account->getKfAccount());
            $request->setFile($this->generateUploadFileFromUrl($avatar));

            $this->client->request($request);
        } catch (\Throwable $exception) {
            $this->logger->error('Failed to upload KfAccount avatar', [
                'account' => $account,
                'exception' => $exception,
            ]);
        }
    }

    /**
     * 读取远程URL的内容，并生成一个上传文件对象
     */
    private function generateUploadFileFromUrl(string $url): UploadedFile
    {
        $content = file_get_contents($url);
        $file = tempnam(sys_get_temp_dir(), 'upload_file');
        file_put_contents($file, $content);
        $name = basename($url);

        return $this->generateUploadFileFromPath($file, $name);
    }

    private function generateUploadFileFromPath(string $path, ?string $name = null): UploadedFile
    {
        if (null === $name) {
            $name = basename($path);
        }

        return new UploadedFile($path, $name);
    }
}
