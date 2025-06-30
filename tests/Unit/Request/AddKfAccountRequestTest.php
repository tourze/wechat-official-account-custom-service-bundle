<?php

namespace WechatOfficialAccountCustomServiceBundle\Tests\Unit\Request;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountCustomServiceBundle\Request\AddKfAccountRequest;

class AddKfAccountRequestTest extends TestCase
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
        $request->setNickname('Test User');
        
        $this->assertEquals('Test User', $request->getNickname());
    }

    public function testPasswordGetterAndSetter(): void
    {
        $request = new AddKfAccountRequest();
        $request->setPassword('password123');
        
        $this->assertEquals('password123', $request->getPassword());
    }

    public function testPasswordIsNullByDefault(): void
    {
        $request = new AddKfAccountRequest();
        
        $this->assertNull($request->getPassword());
    }

    public function testGetRequestOptionsWithoutPassword(): void
    {
        $request = new AddKfAccountRequest();
        $request->setKfAccount('test@account');
        $request->setNickname('Test User');
        
        $options = $request->getRequestOptions();
        
        $expected = [
            'json' => [
                'kf_account' => 'test@account',
                'nickname' => 'Test User',
            ],
        ];
        
        $this->assertEquals($expected, $options);
    }

    public function testGetRequestOptionsWithPassword(): void
    {
        $request = new AddKfAccountRequest();
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