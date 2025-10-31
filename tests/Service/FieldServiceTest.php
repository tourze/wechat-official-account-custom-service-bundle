<?php

declare(strict_types=1);

namespace WechatOfficialAccountCustomServiceBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;
use Tourze\EasyAdminEnumFieldBundle\Service\EnumFieldFactoryInterface;
use WechatOfficialAccountCustomServiceBundle\Enum\KfAccountStatus;
use WechatOfficialAccountCustomServiceBundle\Service\FieldService;

/**
 * @internal
 */
#[CoversClass(FieldService::class)]
final class FieldServiceTest extends TestCase
{
    private FieldService $fieldService;

    protected function setUp(): void
    {
        $enumFieldFactory = $this->createMock(EnumFieldFactoryInterface::class);
        $this->fieldService = new FieldService($enumFieldFactory);
    }

    public function testCreateEnumFieldReturnsEnumFieldInstance(): void
    {
        $property = 'status';
        $label = '状态';
        $enumCases = KfAccountStatus::cases();

        $field = $this->fieldService->createEnumField($property, $label, $enumCases);

        $this->assertInstanceOf(EnumField::class, $field);
    }

    public function testCreateEnumFieldWithEmptyEnumCases(): void
    {
        $property = 'emptyField';
        $label = 'Empty Field';
        $enumCases = [];

        $field = $this->fieldService->createEnumField($property, $label, $enumCases);

        $this->assertInstanceOf(EnumField::class, $field);
    }

    public function testCreateEnumFieldWithSpecificEnumCases(): void
    {
        $property = 'specificStatus';
        $label = '特定状态';
        $enumCases = [KfAccountStatus::ENABLED, KfAccountStatus::DISABLED];

        $field = $this->fieldService->createEnumField($property, $label, $enumCases);

        $this->assertInstanceOf(EnumField::class, $field);
    }

    public function testCreateEnumFieldWithDifferentPropertyNames(): void
    {
        $testCases = [
            ['property' => 'status', 'label' => '状态'],
            ['property' => 'type', 'label' => '类型'],
            ['property' => 'category', 'label' => '分类'],
            ['property' => 'priority_level', 'label' => '优先级'],
        ];

        foreach ($testCases as $testCase) {
            $field = $this->fieldService->createEnumField(
                $testCase['property'],
                $testCase['label'],
                KfAccountStatus::cases()
            );

            $this->assertInstanceOf(EnumField::class, $field);
        }
    }

    public function testCreateEnumFieldWithSingleEnumCase(): void
    {
        $property = 'singleStatus';
        $label = '单一状态';
        $enumCases = [KfAccountStatus::ENABLED];

        $field = $this->fieldService->createEnumField($property, $label, $enumCases);

        $this->assertInstanceOf(EnumField::class, $field);
    }

    public function testCreateEnumFieldReturnsNewInstanceEachTime(): void
    {
        $property = 'status';
        $label = '状态';
        $enumCases = KfAccountStatus::cases();

        $field1 = $this->fieldService->createEnumField($property, $label, $enumCases);
        $field2 = $this->fieldService->createEnumField($property, $label, $enumCases);

        $this->assertInstanceOf(EnumField::class, $field1);
        $this->assertInstanceOf(EnumField::class, $field2);
        $this->assertNotSame($field1, $field2);
    }

    public function testCreateEnumFieldWithUnicodeCharacters(): void
    {
        $property = 'unicode_field';
        $label = '测试字段 🚀 Unicode';
        $enumCases = KfAccountStatus::cases();

        $field = $this->fieldService->createEnumField($property, $label, $enumCases);

        $this->assertInstanceOf(EnumField::class, $field);
    }

    public function testCreateEnumFieldWithAllKfAccountStatusCases(): void
    {
        $property = 'allStatuses';
        $label = '所有状态';
        $enumCases = KfAccountStatus::cases();

        $field = $this->fieldService->createEnumField($property, $label, $enumCases);

        $this->assertInstanceOf(EnumField::class, $field);

        // 验证枚举案例的数量
        $this->assertCount(3, $enumCases);
        $this->assertContains(KfAccountStatus::ENABLED, $enumCases);
        $this->assertContains(KfAccountStatus::DISABLED, $enumCases);
        $this->assertContains(KfAccountStatus::DELETED, $enumCases);
    }

    public function testCreateEnumFieldHandlesVariousEnumCombinations(): void
    {
        // 测试所有案例
        $allCases = KfAccountStatus::cases();
        $field1 = $this->fieldService->createEnumField('all_status', '所有状态', $allCases);
        $this->assertInstanceOf(EnumField::class, $field1);

        // 测试部分案例
        $partialCases = [KfAccountStatus::ENABLED, KfAccountStatus::DELETED];
        $field2 = $this->fieldService->createEnumField('partial_status', '部分状态', $partialCases);
        $this->assertInstanceOf(EnumField::class, $field2);

        // 测试单个案例
        $singleCase = [KfAccountStatus::DISABLED];
        $field3 = $this->fieldService->createEnumField('single_status', '单一状态', $singleCase);
        $this->assertInstanceOf(EnumField::class, $field3);
    }

    public function testCreateEnumFieldWithEmptyLabel(): void
    {
        $property = 'statusWithEmptyLabel';
        $label = '';
        $enumCases = KfAccountStatus::cases();

        $field = $this->fieldService->createEnumField($property, $label, $enumCases);

        $this->assertInstanceOf(EnumField::class, $field);
    }

    public function testCreateEnumFieldPreservesEnumValues(): void
    {
        $enumCases = KfAccountStatus::cases();
        $originalCount = count($enumCases);

        $field = $this->fieldService->createEnumField('test_status', '测试状态', $enumCases);

        $this->assertInstanceOf(EnumField::class, $field);
        // 确保原始数组未被修改
        $this->assertCount($originalCount, $enumCases);
    }

    public function testCreateEnumFieldServiceIsStateless(): void
    {
        // 创建多个字段，验证服务是无状态的
        $field1 = $this->fieldService->createEnumField('status1', '状态1', [KfAccountStatus::ENABLED]);
        $field2 = $this->fieldService->createEnumField('status2', '状态2', [KfAccountStatus::DISABLED]);
        $field3 = $this->fieldService->createEnumField('status3', '状态3', [KfAccountStatus::DELETED]);

        $this->assertInstanceOf(EnumField::class, $field1);
        $this->assertInstanceOf(EnumField::class, $field2);
        $this->assertInstanceOf(EnumField::class, $field3);

        // 验证每个字段都是独立的实例
        $this->assertNotSame($field1, $field2);
        $this->assertNotSame($field2, $field3);
        $this->assertNotSame($field1, $field3);
    }
}
