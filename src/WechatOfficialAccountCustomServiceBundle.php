<?php

namespace WechatOfficialAccountCustomServiceBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;

class WechatOfficialAccountCustomServiceBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \WechatOfficialAccountBundle\WechatOfficialAccountBundle::class => ['all' => true],
        ];
    }
}
