<?php

namespace WechatOfficialAccountCustomServiceBundle\Tests\EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatOfficialAccountCustomServiceBundle\Entity\KfAccount;
use WechatOfficialAccountCustomServiceBundle\EventSubscriber\KfAccountListener;

/**
 * @internal
 */
#[CoversClass(KfAccountListener::class)]
#[RunTestsInSeparateProcesses]
final class KfAccountListenerTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testCanBeInstantiated(): void
    {
        $listener = self::getService(KfAccountListener::class);

        $this->assertInstanceOf(KfAccountListener::class, $listener);
    }

    public function testPostPersistWithSyncingAccount(): void
    {
        $listener = self::getService(KfAccountListener::class);

        $account = $this->createMock(KfAccount::class);
        $account->method('isSyncing')->willReturn(true);

        $this->expectNotToPerformAssertions();
        $listener->postPersist($account);
    }

    public function testPostUpdateWithSyncingAccount(): void
    {
        $listener = self::getService(KfAccountListener::class);

        $account = $this->createMock(KfAccount::class);
        $account->method('isSyncing')->willReturn(true);

        $this->expectNotToPerformAssertions();
        $listener->postUpdate($account);
    }

    public function testPreRemoveWithSyncingAccount(): void
    {
        $listener = self::getService(KfAccountListener::class);

        $account = $this->createMock(KfAccount::class);
        $account->method('isSyncing')->willReturn(true);

        $this->expectNotToPerformAssertions();
        $listener->preRemove($account);
    }
}
