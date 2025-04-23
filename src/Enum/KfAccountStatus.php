<?php

namespace WechatOfficialAccountCustomServiceBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum KfAccountStatus: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case ENABLED = 'enabled';
    case DISABLED = 'disabled';
    case DELETED = 'deleted';

    public function getLabel(): string
    {
        return match ($this) {
            self::ENABLED => '启用',
            self::DISABLED => '禁用',
            self::DELETED => '已删除',
        };
    }
}
