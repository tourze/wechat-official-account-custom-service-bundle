<?php

namespace WechatOfficialAccountCustomServiceBundle\Tests;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountBundle\WechatOfficialAccountBundle;
use WechatOfficialAccountCustomServiceBundle\WechatOfficialAccountCustomServiceBundle;

class WechatOfficialAccountCustomServiceBundleTest extends TestCase
{
    public function testGetBundleDependencies_includesRequiredBundles(): void
    {
        $dependencies = WechatOfficialAccountCustomServiceBundle::getBundleDependencies();
        
        $this->assertArrayHasKey(WechatOfficialAccountBundle::class, $dependencies);
        $this->assertSame(['all' => true], $dependencies[WechatOfficialAccountBundle::class]);
    }
} 