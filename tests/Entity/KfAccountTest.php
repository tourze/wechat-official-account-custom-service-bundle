<?php

namespace WechatOfficialAccountCustomServiceBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountCustomServiceBundle\Entity\KfAccount;
use WechatOfficialAccountCustomServiceBundle\Enum\KfAccountStatus;

/**
 * @internal
 */
#[CoversClass(KfAccount::class)]
final class KfAccountTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new KfAccount();
    }

    /**
     * @return iterable<array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'kfAccount' => ['kfAccount', 'test_value'],
            'nickname' => ['nickname', 'test_value'],
            'syncing' => ['syncing', true],
        ];
    }

    public function testGetterAndSetterWithValidData(): void
    {
        // 使用具体类 Account 创建 Mock 的原因：
        // 1. Account 是 Doctrine 实体类，没有对应的接口定义
        // 2. 测试中只需要验证关联关系的 setter/getter 行为，不涉及复杂的业务逻辑
        // 3. KfAccount 与 Account 的关联是核心业务需求，需要确保类型安全
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

        $createTime = new \DateTimeImmutable();
        $updateTime = new \DateTimeImmutable();
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

    public function testGetterAndSetterWithNullableFields(): void
    {
        // 使用具体类 Account 创建 Mock 的原因：
        // 1. Account 是 Doctrine 实体类，没有对应的接口定义
        // 2. 测试中只需要验证关联关系的 setter/getter 行为，不涉及复杂的业务逻辑
        // 3. KfAccount 与 Account 的关联是核心业务需求，需要确保类型安全
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

    public function testSetStatusWithDifferentStatuses(): void
    {
        $kfAccount = new KfAccount();

        $kfAccount->setStatus(KfAccountStatus::ENABLED);
        $this->assertSame(KfAccountStatus::ENABLED, $kfAccount->getStatus());

        $kfAccount->setStatus(KfAccountStatus::DISABLED);
        $this->assertSame(KfAccountStatus::DISABLED, $kfAccount->getStatus());

        $kfAccount->setStatus(KfAccountStatus::DELETED);
        $this->assertSame(KfAccountStatus::DELETED, $kfAccount->getStatus());
    }

    public function testIdDefaultIsNull(): void
    {
        $kfAccount = new KfAccount();
        $this->assertNull($kfAccount->getId());
    }

    public function testSyncingDefaultIsFalse(): void
    {
        $kfAccount = new KfAccount();
        $this->assertFalse($kfAccount->isSyncing());
    }
}
