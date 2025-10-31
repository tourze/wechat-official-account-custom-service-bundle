<?php

declare(strict_types=1);

namespace WechatOfficialAccountCustomServiceBundle\Service;

use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

/**
 * 字段工厂接口
 * 提供跨模块字段创建的抽象层，避免直接依赖其他模块的具体实现。
 */
interface FieldFactoryInterface
{
    /**
     * 为指定属性创建枚举字段
     *
     * @param string $property 属性名称
     * @param string $label 字段标签
     * @param array<\BackedEnum> $enumCases 枚举选项
     */
    public function createEnumField(string $property, string $label, array $enumCases): EnumField;
}
