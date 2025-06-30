<?php

namespace WechatOfficialAccountCustomServiceBundle\Tests\Unit\Request;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountCustomServiceBundle\Request\UpdateKfAccountRequest;

class UpdateKfAccountRequestTest extends TestCase
{
    public function testGetRequestPath(): void
    {
        $request = new UpdateKfAccountRequest();
        
        $this->assertEquals('https://api.weixin.qq.com/customservice/kfaccount/update', $request->getRequestPath());
    }

    public function testKfAccountGetterAndSetter(): void
    {
        $request = new UpdateKfAccountRequest();
        $request->setKfAccount('test@account');
        
        $this->assertEquals('test@account', $request->getKfAccount());
    }

    public function testNicknameGetterAndSetter(): void
    {
        $request = new UpdateKfAccountRequest();
        $request->setNickname('Test User');
        
        $this->assertEquals('Test User', $request->getNickname());
    }

    public function testPasswordGetterAndSetter(): void
    {
        $request = new UpdateKfAccountRequest();
        $request->setPassword('password123');
        
        $this->assertEquals('password123', $request->getPassword());
    }

    public function testGetRequestOptions(): void
    {
        $request = new UpdateKfAccountRequest();
        $request->setKfAccount('test@account');
        $request->setNickname('Test User');
        $request->setPassword('password123');
        
        $options = $request->getRequestOptions();
        
        $expected = [
            'json' => [
                'kf_account' => 'test@account',
                'nickname' => 'Test User',
                'password' => 'password123',
            ],
        ];
        
        $this->assertEquals($expected, $options);
    }
}