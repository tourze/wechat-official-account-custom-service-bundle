<?php

namespace WechatOfficialAccountCustomServiceBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WechatOfficialAccountCustomServiceBundle\DependencyInjection\WechatOfficialAccountCustomServiceExtension;

class WechatOfficialAccountCustomServiceExtensionTest extends TestCase
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
        
        $this->expectNotToPerformAssertions();
        
        try {
            $extension->load([], $container);
        } catch (\Exception $e) {
            $this->fail('Extension load should not throw exception: ' . $e->getMessage());
        }
    }
}