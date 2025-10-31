<?php

namespace WechatOfficialAccountCustomServiceBundle\Request;

use WechatOfficialAccountBundle\Request\WithAccountRequest;

class GetKfAccountListRequest extends WithAccountRequest
{
    public function getRequestPath(): string
    {
        return 'https://api.weixin.qq.com/cgi-bin/customservice/getkflist';
    }

    public function getRequestOptions(): ?array
    {
        return [];
    }

    public function getRequestMethod(): ?string
    {
        return 'GET';
    }
}
