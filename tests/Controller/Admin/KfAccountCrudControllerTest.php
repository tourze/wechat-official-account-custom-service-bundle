<?php

declare(strict_types=1);

namespace WechatOfficialAccountCustomServiceBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;
use Tourze\EasyAdminEnumFieldBundle\Service\EnumFieldFactoryInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatOfficialAccountCustomServiceBundle\Controller\Admin\KfAccountCrudController;
use WechatOfficialAccountCustomServiceBundle\Entity\KfAccount;
use WechatOfficialAccountCustomServiceBundle\Enum\KfAccountStatus;
use WechatOfficialAccountCustomServiceBundle\Service\FieldService;

/**
 * @internal
 */
#[CoversClass(KfAccountCrudController::class)]
#[RunTestsInSeparateProcesses]
final class KfAccountCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AbstractCrudController<KfAccount>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(KfAccountCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '所属公众号' => ['所属公众号'];
        yield '客服账号' => ['客服账号'];
        yield '客服昵称' => ['客服昵称'];
        yield '客服密码' => ['客服密码'];
        yield '客服头像' => ['客服头像'];
        yield '状态' => ['状态'];
        yield '客服ID' => ['客服ID'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'account' => ['account'];
        yield 'kfAccount' => ['kfAccount'];
        yield 'nickname' => ['nickname'];
        yield 'password' => ['password'];
        yield 'avatar' => ['avatar'];
        yield 'status' => ['status'];
        yield 'kfId' => ['kfId'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'account' => ['account'];
        yield 'kfAccount' => ['kfAccount'];
        yield 'nickname' => ['nickname'];
        yield 'password' => ['password'];
        yield 'avatar' => ['avatar'];
        yield 'status' => ['status'];
        yield 'kfId' => ['kfId'];
    }

    public function testConfigureFields(): void
    {
        $enumFieldFactory = $this->createMock(EnumFieldFactoryInterface::class);

        // 配置 Mock 返回 EnumField 实例
        $enumField = EnumField::new('status', '状态');
        $enumFieldFactory->expects($this->once())
            ->method('createEnumField')
            ->with('status', '状态', Assert::callback(function ($param) {
                return is_array($param);
            }))
            ->willReturn($enumField)
        ;

        $fieldService = new FieldService($enumFieldFactory);
        $controller = new KfAccountCrudController($fieldService);
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields);

        /** @var array<string> $fieldTypes */
        $fieldTypes = [];
        foreach ($fields as $field) {
            if ($field instanceof FieldInterface) {
                $fieldTypes[] = get_class($field);
            }
        }

        $this->assertContains(IdField::class, $fieldTypes);
        $this->assertContains(AssociationField::class, $fieldTypes);
        $this->assertContains(TextField::class, $fieldTypes);
        $this->assertContains(UrlField::class, $fieldTypes);
        $this->assertContains(EnumField::class, $fieldTypes);
        $this->assertContains(DateTimeField::class, $fieldTypes);
    }

    public function testFieldConfiguration(): void
    {
        $enumFieldFactory = $this->createMock(EnumFieldFactoryInterface::class);
        $fieldService = new FieldService($enumFieldFactory);
        $controller = new KfAccountCrudController($fieldService);
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertCount(10, $fields);
        $this->assertNotEmpty($fields);
    }

    public function testControllerInstantiation(): void
    {
        $enumFieldFactory = $this->createMock(EnumFieldFactoryInterface::class);
        $fieldService = new FieldService($enumFieldFactory);
        $controller = new KfAccountCrudController($fieldService);
        $this->assertInstanceOf(KfAccountCrudController::class, $controller);
    }

    public function testDifferentPageConfigurations(): void
    {
        $enumFieldFactory = $this->createMock(EnumFieldFactoryInterface::class);
        $fieldService = new FieldService($enumFieldFactory);
        $controller = new KfAccountCrudController($fieldService);

        $indexFields = iterator_to_array($controller->configureFields('index'));
        $newFields = iterator_to_array($controller->configureFields('new'));
        $editFields = iterator_to_array($controller->configureFields('edit'));

        $this->assertNotEmpty($indexFields);
        $this->assertNotEmpty($newFields);
        $this->assertNotEmpty($editFields);

        $this->assertGreaterThanOrEqual(count($newFields), count($indexFields));
    }

    public function testValidationErrors(): void
    {
        $client = $this->createAuthenticatedClient();

        // 提交空的必填字段来测试验证
        $client->request('POST', $this->generateAdminUrl('new'), [
            'KfAccount' => [
                'kfAccount' => '', // 必填字段为空，这是我们想要测试验证的字段
                'nickname' => 'Test Nickname',
                'status' => KfAccountStatus::ENABLED->value, // 提交枚举的底层值，结合字段转换为枚举实例
            ],
        ]);

        $response = $client->getResponse();
        $this->assertSame(422, $response->getStatusCode());
        $content = $response->getContent();
        $this->assertIsString($content);

        // 验证表单验证错误信息（支持中英文）
        $hasValidationError = str_contains($content, '不能为空') || str_contains($content, 'should not be blank');
        $this->assertTrue($hasValidationError, 'Response should contain validation error message');

        // 额外验证中文错误信息
        $this->assertStringContainsString('不能为空', $content);
    }
}
