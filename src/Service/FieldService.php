<?php

declare(strict_types=1);

namespace WechatOfficialAccountCustomServiceBundle\Service;

use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;
use Tourze\EasyAdminEnumFieldBundle\Service\EnumFieldFactoryInterface;

/**
 * 字段服务类，为字段创建提供抽象层。
 * 该服务根据架构边界封装跨模块字段使用。
 */
final class FieldService implements FieldFactoryInterface
{
    public function __construct(
        private readonly EnumFieldFactoryInterface $enumFieldFactory,
    ) {
    }

    /**
     * 为指定属性创建枚举字段。
     *
     * @param string $property 属性名称
     * @param string $label 字段标签
     * @param array<\BackedEnum> $enumCases 枚举选项
     */
    public function createEnumField(string $property, string $label, array $enumCases): EnumField
    {
        return $this->enumFieldFactory->createEnumField($property, $label, $enumCases);
    }
}
