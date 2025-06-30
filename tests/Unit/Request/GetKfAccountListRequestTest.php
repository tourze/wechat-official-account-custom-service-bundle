<?php

namespace WechatOfficialAccountCustomServiceBundle\Tests\Unit\Request;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountCustomServiceBundle\Request\GetKfAccountListRequest;

class GetKfAccountListRequestTest extends TestCase
{
    public function testGetRequestPath(): void
    {
        $request = new GetKfAccountListRequest();
        
        $this->assertEquals('https://api.weixin.qq.com/cgi-bin/customservice/getkflist', $request->getRequestPath());
    }

    public function testGetRequestOptions(): void
    {
        $request = new GetKfAccountListRequest();
        
        $this->assertEquals([], $request->getRequestOptions());
    }

    public function testGetRequestMethod(): void
    {
        $request = new GetKfAccountListRequest();
        
        $this->assertEquals('GET', $request->getRequestMethod());
    }
}