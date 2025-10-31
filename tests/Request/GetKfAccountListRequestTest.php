<?php

namespace WechatOfficialAccountCustomServiceBundle\Tests\Request;

use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use WechatOfficialAccountCustomServiceBundle\Request\GetKfAccountListRequest;

/**
 * @internal
 */
#[CoversClass(GetKfAccountListRequest::class)]
final class GetKfAccountListRequestTest extends RequestTestCase
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
