<?php

namespace WechatOfficialAccountCustomServiceBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
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
    use SnowflakeKeyAware;
    use TimestampableAware;
    use BlameableAware;

    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(name: 'account_id', referencedColumnName: 'id', nullable: false, options: ['comment' => '所属公众号'])]
    private Account $account;

    #[ORM\Column(length: 100, unique: true, options: ['comment' => '客服账号'])]
    #[Assert\NotBlank(message: '客服账号不能为空')]
    #[Assert\Length(max: 100, maxMessage: '客服账号长度不能超过{{ limit }}个字符')]
    private string $kfAccount;

    #[ORM\Column(length: 100, options: ['comment' => '客服昵称'])]
    #[Assert\NotBlank(message: '客服昵称不能为空')]
    #[Assert\Length(max: 100, maxMessage: '客服昵称长度不能超过{{ limit }}个字符')]
    private string $nickname;

    #[ORM\Column(length: 32, nullable: true, options: ['comment' => '客服密码'])]
    #[Assert\Length(max: 32, maxMessage: '客服密码长度不能超过{{ limit }}个字符')]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '客服头像URL'])]
    #[Assert\Url(message: '请提供有效的头像URL')]
    #[Assert\Length(max: 255, maxMessage: '头像URL长度不能超过{{ limit }}个字符')]
    private ?string $avatar = null;

    #[ORM\Column(type: Types::STRING, enumType: KfAccountStatus::class, options: ['comment' => '账号状态'])]
    #[Assert\Choice(callback: [KfAccountStatus::class, 'cases'], message: '请选择有效的账号状态')]
    private KfAccountStatus $status = KfAccountStatus::ENABLED;

    #[ORM\Column(length: 32, nullable: true, options: ['comment' => '客服ID'])]
    #[Assert\Length(max: 32, maxMessage: '客服ID长度不能超过{{ limit }}个字符')]
    private ?string $kfId = null;

    #[Assert\Type(type: 'bool', message: 'syncing属性必须是布尔类型')]
    private bool $syncing = false;

    public function isSyncing(): bool
    {
        return $this->syncing;
    }

    public function setSyncing(bool $syncing): void
    {
        $this->syncing = $syncing;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }

    public function getKfAccount(): string
    {
        return $this->kfAccount;
    }

    public function setKfAccount(string $kfAccount): void
    {
        $this->kfAccount = $kfAccount;
    }

    public function getNickname(): string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): void
    {
        $this->nickname = $nickname;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getStatus(): KfAccountStatus
    {
        return $this->status;
    }

    public function setStatus(KfAccountStatus $status): void
    {
        $this->status = $status;
    }

    public function getKfId(): ?string
    {
        return $this->kfId;
    }

    public function setKfId(?string $kfId): void
    {
        $this->kfId = $kfId;
    }

    public function __toString(): string
    {
        return $this->kfAccount ?? 'New KfAccount';
    }
}
