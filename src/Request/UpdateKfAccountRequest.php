<?php

namespace WechatOfficialAccountCustomServiceBundle\Request;

use WechatOfficialAccountBundle\Request\WithAccountRequest;

class UpdateKfAccountRequest extends WithAccountRequest
{
    private string $kfAccount;

    private string $nickname;

    private string $password;

    public function getRequestPath(): string
    {
        return 'https://api.weixin.qq.com/customservice/kfaccount/update';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'json' => [
                'kf_account' => $this->getKfAccount(),
                'nickname' => $this->getNickname(),
                'password' => $this->getPassword(),
            ],
        ];
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

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}
