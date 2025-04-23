<?php

namespace WechatOfficialAccountCustomServiceBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '公众号客服')]
class WechatOfficialAccountCustomServiceBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \WechatOfficialAccountBundle\WechatOfficialAccountBundle::class => ['all' => true],
        ];
    }
}
