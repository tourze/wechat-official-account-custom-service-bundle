<?php

declare(strict_types=1);

namespace WechatOfficialAccountCustomServiceBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use WechatOfficialAccountCustomServiceBundle\Service\AdminMenu;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // 无需特殊设置
    }

    public function testServiceInstance(): void
    {
        // 从容器中获取 AdminMenu 服务
        $adminMenu = self::getService(AdminMenu::class);

        // 测试 AdminMenu 能够正常实例化
        $this->assertInstanceOf(AdminMenu::class, $adminMenu);
    }

    public function testGetMenuItems(): void
    {
        $adminMenu = self::getService(AdminMenu::class);
        $menuItems = iterator_to_array($adminMenu->getMenuItems());

        $this->assertNotEmpty($menuItems);
        $this->assertCount(2, $menuItems);
    }

    public function testMenuItemsAreIterable(): void
    {
        $adminMenu = self::getService(AdminMenu::class);
        $menuItems = iterator_to_array($adminMenu->getMenuItems());
        $this->assertNotEmpty($menuItems);

        $count = 0;
        foreach ($menuItems as $item) {
            ++$count;
        }

        $this->assertSame(2, $count);
    }

    public function testMenuItemsHaveLabels(): void
    {
        $adminMenu = self::getService(AdminMenu::class);
        $menuItems = iterator_to_array($adminMenu->getMenuItems());

        $this->assertCount(2, $menuItems);
    }
}
