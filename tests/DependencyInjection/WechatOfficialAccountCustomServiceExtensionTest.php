<?php

namespace WechatOfficialAccountCustomServiceBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use WechatOfficialAccountCustomServiceBundle\DependencyInjection\WechatOfficialAccountCustomServiceExtension;

/**
 * @internal
 */
#[CoversClass(WechatOfficialAccountCustomServiceExtension::class)]
final class WechatOfficialAccountCustomServiceExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    public function testCanBeInstantiated(): void
    {
        $extension = new WechatOfficialAccountCustomServiceExtension();

        $this->assertInstanceOf(WechatOfficialAccountCustomServiceExtension::class, $extension);
    }

    public function testLoadDoesNotThrowException(): void
    {
        $extension = new WechatOfficialAccountCustomServiceExtension();
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');

        $this->expectNotToPerformAssertions();

        try {
            $extension->load([], $container);
        } catch (\Exception $e) {
            self::fail('Extension load should not throw exception: ' . $e->getMessage());
        }
    }
}
