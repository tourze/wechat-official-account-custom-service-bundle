<?php

namespace WechatOfficialAccountCustomServiceBundle\Request;

use WechatOfficialAccountBundle\Request\WithAccountRequest;

class DeleteKfAccountRequest extends WithAccountRequest
{
    private string $kfAccount;

    public function getRequestPath(): string
    {
        return 'https://api.weixin.qq.com/customservice/kfaccount/del';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'json' => [
                'kf_account' => $this->getKfAccount(),
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
}
