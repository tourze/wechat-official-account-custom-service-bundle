<?php

namespace WechatOfficialAccountCustomServiceBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use WechatOfficialAccountCustomServiceBundle\Enum\KfAccountStatus;

/**
 * @internal
 */
#[CoversClass(KfAccountStatus::class)]
final class KfAccountStatusTest extends AbstractEnumTestCase
{
    #[TestWith([KfAccountStatus::ENABLED, 'enabled', '启用'])]
    #[TestWith([KfAccountStatus::DISABLED, 'disabled', '禁用'])]
    #[TestWith([KfAccountStatus::DELETED, 'deleted', '已删除'])]
    public function testValueAndLabelForAllCases(KfAccountStatus $status, string $expectedValue, string $expectedLabel): void
    {
        $this->assertSame($expectedValue, $status->value);
        $this->assertSame($expectedLabel, $status->getLabel());
    }

    public function testCasesReturnsAllEnumValues(): void
    {
        $cases = KfAccountStatus::cases();

        $this->assertCount(3, $cases);
        $this->assertContains(KfAccountStatus::ENABLED, $cases);
        $this->assertContains(KfAccountStatus::DISABLED, $cases);
        $this->assertContains(KfAccountStatus::DELETED, $cases);
    }

    public function testGenOptionsReturnsCorrectStructure(): void
    {
        $options = KfAccountStatus::genOptions();
        $this->assertCount(3, $options);

        foreach ($options as $item) {
            $this->assertArrayHasKey('value', $item);
            $this->assertArrayHasKey('label', $item);
            $this->assertArrayHasKey('text', $item);
            $this->assertArrayHasKey('name', $item);

            $this->assertContains($item['value'], ['enabled', 'disabled', 'deleted']);
            $this->assertContains($item['label'], ['启用', '禁用', '已删除']);
        }

        // 验证特定条目
        $enabledItem = array_filter($options, fn ($item) => 'enabled' === $item['value']);
        $this->assertCount(1, $enabledItem);
        $enabledItem = reset($enabledItem);
        $this->assertNotFalse($enabledItem, 'Expected to find enabled item in options');
        $this->assertSame('启用', $enabledItem['label']);
    }

    public function testGenOptionsReturnsOptionsInCorrectOrder(): void
    {
        $options = KfAccountStatus::genOptions();

        $values = array_column($options, 'value');

        // 测试顺序与枚举定义顺序一致
        $this->assertSame('enabled', $values[0]);
        $this->assertSame('disabled', $values[1]);
        $this->assertSame('deleted', $values[2]);
    }

    public function testToSelectItemReturnsCorrectStructure(): void
    {
        $item = KfAccountStatus::ENABLED->toSelectItem();
        $this->assertArrayHasKey('value', $item);
        $this->assertArrayHasKey('label', $item);
        $this->assertArrayHasKey('text', $item);
        $this->assertArrayHasKey('name', $item);

        $this->assertSame('enabled', $item['value']);
        $this->assertSame('启用', $item['label']);
        $this->assertSame('启用', $item['text']);
        $this->assertSame('启用', $item['name']);
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $array = KfAccountStatus::DISABLED->toArray();
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);

        $this->assertSame('disabled', $array['value']);
        $this->assertSame('禁用', $array['label']);
    }

    public function testValueUniqueness(): void
    {
        $values = array_map(fn (KfAccountStatus $case) => $case->value, KfAccountStatus::cases());
        $this->assertSame(array_unique($values), $values, 'Enum values must be unique');
    }

    public function testLabelUniqueness(): void
    {
        $labels = array_map(fn (KfAccountStatus $case) => $case->getLabel(), KfAccountStatus::cases());
        $this->assertSame(array_unique($labels), $labels, 'Enum labels must be unique');
    }
}
