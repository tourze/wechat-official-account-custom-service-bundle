<?php

namespace WechatOfficialAccountCustomServiceBundle\Tests\Request;

use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use WechatOfficialAccountCustomServiceBundle\Request\AddKfAccountRequest;

/**
 * @internal
 */
#[CoversClass(AddKfAccountRequest::class)]
final class AddKfAccountRequestTest extends RequestTestCase
{
    public function testGetRequestPath(): void
    {
        $request = new AddKfAccountRequest();

        $this->assertEquals('https://api.weixin.qq.com/customservice/kfaccount/add', $request->getRequestPath());
    }

    public function testKfAccountGetterAndSetter(): void
    {
        $request = new AddKfAccountRequest();
        $request->setKfAccount('test@account');

        $this->assertEquals('test@account', $request->getKfAccount());
    }

    public function testNicknameGetterAndSetter(): void
    {
        $request = new AddKfAccountRequest();
        $request->setNickname('Test Nickname');

        $this->assertEquals('Test Nickname', $request->getNickname());
    }

    public function testPasswordGetterAndSetter(): void
    {
        $request = new AddKfAccountRequest();
        $request->setPassword('test_password');

        $this->assertEquals('test_password', $request->getPassword());
    }

    public function testGetRequestOptions(): void
    {
        $request = new AddKfAccountRequest();
        $request->setKfAccount('test@account');
        $request->setNickname('Test Nickname');
        $request->setPassword('test_password');

        $options = $request->getRequestOptions();

        $expected = [
            'json' => [
                'kf_account' => 'test@account',
                'nickname' => 'Test Nickname',
                'password' => 'test_password',
            ],
        ];

        $this->assertEquals($expected, $options);
    }

    public function testGetRequestOptionsWithOptionalFields(): void
    {
        $request = new AddKfAccountRequest();
        $request->setKfAccount('test@account');
        $request->setNickname('Test Nickname');

        $options = $request->getRequestOptions();

        $expected = [
            'json' => [
                'kf_account' => 'test@account',
                'nickname' => 'Test Nickname',
            ],
        ];

        $this->assertEquals($expected, $options);
    }
}
