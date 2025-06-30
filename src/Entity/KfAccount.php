<?php

namespace WechatOfficialAccountCustomServiceBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountCustomServiceBundle\Enum\KfAccountStatus;
use WechatOfficialAccountCustomServiceBundle\Repository\KfAccountRepository;

#[ORM\Table(name: 'wechat_kf_account', options: ['comment' => '微信公众号客服账号表'])]
#[ORM\Entity(repositoryClass: KfAccountRepository::class)]
class KfAccount implements \Stringable
{

    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(name: 'account_id', referencedColumnName: 'id', nullable: false, options: ['comment' => '所属公众号'])]
    private Account $account;

    #[ORM\Column(length: 100, unique: true, options: ['comment' => '客服账号'])]
    private string $kfAccount;

    #[ORM\Column(length: 100, options: ['comment' => '客服昵称'])]
    private string $nickname;

    #[ORM\Column(length: 32, nullable: true, options: ['comment' => '客服密码'])]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '客服头像URL'])]
    private ?string $avatar = null;

    #[ORM\Column(type: Types::STRING, enumType: KfAccountStatus::class, options: ['comment' => '账号状态'])]
    private KfAccountStatus $status = KfAccountStatus::ENABLED;

    #[ORM\Column(length: 32, nullable: true, options: ['comment' => '客服ID'])]
    private ?string $kfId = null;

    use SnowflakeKeyAware;
    use TimestampableAware;
    use BlameableAware;

    private bool $syncing = false;



    public function isSyncing(): bool
    {
        return $this->syncing;
    }

    public function setSyncing(bool $syncing): static
    {
        $this->syncing = $syncing;

        return $this;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getKfAccount(): string
    {
        return $this->kfAccount;
    }

    public function setKfAccount(string $kfAccount): self
    {
        $this->kfAccount = $kfAccount;

        return $this;
    }

    public function getNickname(): string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getStatus(): KfAccountStatus
    {
        return $this->status;
    }

    public function setStatus(KfAccountStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getKfId(): ?string
    {
        return $this->kfId;
    }

    public function setKfId(?string $kfId): self
    {
        $this->kfId = $kfId;

        return $this;
    }

    public function __toString(): string
    {
        return $this->kfAccount ?? 'New KfAccount';
    }
}
