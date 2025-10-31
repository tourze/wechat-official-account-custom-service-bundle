<?php

namespace WechatOfficialAccountCustomServiceBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\EasyAdminEnumFieldBundle\EasyAdminEnumFieldBundle;
use WechatOfficialAccountBundle\WechatOfficialAccountBundle;

class WechatOfficialAccountCustomServiceBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            WechatOfficialAccountBundle::class => ['all' => true],
            DoctrineBundle::class => ['all' => true],
            EasyAdminEnumFieldBundle::class => ['all' => true],
        ];
    }
}
