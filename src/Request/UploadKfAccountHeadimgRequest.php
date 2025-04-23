<?php

namespace WechatOfficialAccountCustomServiceBundle\Request;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use WechatOfficialAccountBundle\Request\WithAccountRequest;

class UploadKfAccountHeadimgRequest extends WithAccountRequest
{
    private string $kfAccount;

    private UploadedFile $file;

    public function getRequestPath(): string
    {
        return 'https://api.weixin.qq.com/customservice/kfaccount/uploadheadimg';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'multipart' => [
                [
                    'name' => 'kf_account',
                    'contents' => $this->getKfAccount(),
                ],
                [
                    'name' => 'media',
                    'contents' => fopen($this->getFile()->getPathname(), 'r'),
                    'filename' => $this->getFile()->getClientOriginalName(),
                ],
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

    public function getFile(): UploadedFile
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file): void
    {
        $this->file = $file;
    }
}
