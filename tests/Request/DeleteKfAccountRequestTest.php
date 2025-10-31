<?php

namespace WechatOfficialAccountCustomServiceBundle\Tests\Request;

use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use WechatOfficialAccountCustomServiceBundle\Request\DeleteKfAccountRequest;

/**
 * @internal
 */
#[CoversClass(DeleteKfAccountRequest::class)]
final class DeleteKfAccountRequestTest extends RequestTestCase
{
    public function testGetRequestPath(): void
    {
        $request = new DeleteKfAccountRequest();

        $this->assertEquals('https://api.weixin.qq.com/customservice/kfaccount/del', $request->getRequestPath());
    }

    public function testKfAccountGetterAndSetter(): void
    {
        $request = new DeleteKfAccountRequest();
        $request->setKfAccount('test@account');

        $this->assertEquals('test@account', $request->getKfAccount());
    }

    public function testGetRequestOptions(): void
    {
        $request = new DeleteKfAccountRequest();
        $request->setKfAccount('test@account');

        $options = $request->getRequestOptions();

        $expected = [
            'json' => [
                'kf_account' => 'test@account',
            ],
        ];

        $this->assertEquals($expected, $options);
    }
}
