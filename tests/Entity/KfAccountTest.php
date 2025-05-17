<?php

namespace WechatOfficialAccountCustomServiceBundle\Tests\Entity;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountCustomServiceBundle\Entity\KfAccount;
use WechatOfficialAccountCustomServiceBundle\Enum\KfAccountStatus;

class KfAccountTest extends TestCase
{
    public function testGetterAndSetter_withValidData(): void
    {
        $account = $this->createMock(Account::class);
        
        $kfAccount = new KfAccount();
        $kfAccount->setAccount($account);
        $kfAccount->setKfAccount('test_kf@account');
        $kfAccount->setNickname('测试客服');
        $kfAccount->setPassword('password123');
        $kfAccount->setAvatar('http://example.com/avatar.jpg');
        $kfAccount->setStatus(KfAccountStatus::ENABLED);
        $kfAccount->setKfId('kf_123456');
        $kfAccount->setCreatedBy('admin');
        $kfAccount->setUpdatedBy('admin');
        
        $createTime = new DateTimeImmutable();
        $updateTime = new DateTimeImmutable();
        $kfAccount->setCreateTime($createTime);
        $kfAccount->setUpdateTime($updateTime);
        $kfAccount->setSyncing(true);

        // 断言所有getter方法返回正确的值
        $this->assertSame($account, $kfAccount->getAccount());
        $this->assertSame('test_kf@account', $kfAccount->getKfAccount());
        $this->assertSame('测试客服', $kfAccount->getNickname());
        $this->assertSame('password123', $kfAccount->getPassword());
        $this->assertSame('http://example.com/avatar.jpg', $kfAccount->getAvatar());
        $this->assertSame(KfAccountStatus::ENABLED, $kfAccount->getStatus());
        $this->assertSame('kf_123456', $kfAccount->getKfId());
        $this->assertSame('admin', $kfAccount->getCreatedBy());
        $this->assertSame('admin', $kfAccount->getUpdatedBy());
        $this->assertSame($createTime, $kfAccount->getCreateTime());
        $this->assertSame($updateTime, $kfAccount->getUpdateTime());
        $this->assertTrue($kfAccount->isSyncing());
    }
    
    public function testGetterAndSetter_withNullableFields(): void
    {
        $account = $this->createMock(Account::class);
        
        $kfAccount = new KfAccount();
        $kfAccount->setAccount($account);
        $kfAccount->setKfAccount('test_kf@account');
        $kfAccount->setNickname('测试客服');
        $kfAccount->setPassword(null);
        $kfAccount->setAvatar(null);
        $kfAccount->setKfId(null);
        $kfAccount->setCreatedBy(null);
        $kfAccount->setUpdatedBy(null);
        $kfAccount->setCreateTime(null);
        $kfAccount->setUpdateTime(null);

        // 断言可空字段正确处理null值
        $this->assertNull($kfAccount->getPassword());
        $this->assertNull($kfAccount->getAvatar());
        $this->assertNull($kfAccount->getKfId());
        $this->assertNull($kfAccount->getCreatedBy());
        $this->assertNull($kfAccount->getUpdatedBy());
        $this->assertNull($kfAccount->getCreateTime());
        $this->assertNull($kfAccount->getUpdateTime());
    }
    
    public function testSetStatus_withDifferentStatuses(): void
    {
        $kfAccount = new KfAccount();
        
        $kfAccount->setStatus(KfAccountStatus::ENABLED);
        $this->assertSame(KfAccountStatus::ENABLED, $kfAccount->getStatus());
        
        $kfAccount->setStatus(KfAccountStatus::DISABLED);
        $this->assertSame(KfAccountStatus::DISABLED, $kfAccount->getStatus());
        
        $kfAccount->setStatus(KfAccountStatus::DELETED);
        $this->assertSame(KfAccountStatus::DELETED, $kfAccount->getStatus());
    }
    
    public function testId_defaultIsNull(): void
    {
        $kfAccount = new KfAccount();
        $this->assertNull($kfAccount->getId());
    }
    
    public function testSyncing_defaultIsFalse(): void
    {
        $kfAccount = new KfAccount();
        $this->assertFalse($kfAccount->isSyncing());
    }
} 