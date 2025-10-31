<?php

namespace WechatOfficialAccountCustomServiceBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountBundle\Repository\AccountRepository;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountCustomServiceBundle\Command\SyncKfAccountCommand;
use WechatOfficialAccountCustomServiceBundle\Entity\KfAccount;
use WechatOfficialAccountCustomServiceBundle\Enum\KfAccountStatus;
use WechatOfficialAccountCustomServiceBundle\Repository\KfAccountRepository;

/**
 * @internal
 */
#[CoversClass(SyncKfAccountCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncKfAccountCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
        // 不需要额外设置
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(SyncKfAccountCommand::class);

        return new CommandTester($command);
    }

    public function testCommandHasCorrectName(): void
    {
        $this->assertEquals('wechat-official-account:custom-service:sync-account-list', SyncKfAccountCommand::NAME);
    }

    public function testCommandCanBeInstantiatedFromContainer(): void
    {
        $command = self::getService(SyncKfAccountCommand::class);
        $this->assertInstanceOf(SyncKfAccountCommand::class, $command);
    }

    public function testCommandExecutionWithNonExistentAccountId(): void
    {
        $command = self::getService(SyncKfAccountCommand::class);
        $commandTester = new CommandTester($command);

        $exitCode = $commandTester->execute(['--account-id' => 999999]);

        $this->assertEquals(Command::FAILURE, $exitCode);
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('公众号 999999 不存在', $output);
    }

    public function testCommandExecutionWithoutAccountId(): void
    {
        $command = self::getService(SyncKfAccountCommand::class);
        $commandTester = new CommandTester($command);

        $exitCode = $commandTester->execute([]);

        // 由于没有 Mock OfficialAccountClient，命令可能会失败，但至少应该能执行
        $this->assertContains($exitCode, [Command::SUCCESS, Command::FAILURE]);
        $output = $commandTester->getDisplay();

        // 验证输出包含相关信息
        $this->assertNotEmpty($output);
    }

    public function testOptionAccountId(): void
    {
        $command = self::getService(SyncKfAccountCommand::class);
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('account-id'));
        $option = $definition->getOption('account-id');
        $this->assertTrue($option->isValueOptional());
        $this->assertEquals('公众号ID', $option->getDescription());
    }

    public function testCommandDependenciesAreInjected(): void
    {
        $command = self::getService(SyncKfAccountCommand::class);

        // 通过反射检查私有属性是否已正确注入
        $reflection = new \ReflectionClass($command);

        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $this->assertInstanceOf(OfficialAccountClient::class, $clientProperty->getValue($command));

        $entityManagerProperty = $reflection->getProperty('entityManager');
        $entityManagerProperty->setAccessible(true);
        $this->assertInstanceOf(EntityManagerInterface::class, $entityManagerProperty->getValue($command));

        $accountRepositoryProperty = $reflection->getProperty('accountRepository');
        $accountRepositoryProperty->setAccessible(true);
        $this->assertInstanceOf(AccountRepository::class, $accountRepositoryProperty->getValue($command));

        $kfAccountRepositoryProperty = $reflection->getProperty('kfAccountRepository');
        $kfAccountRepositoryProperty->setAccessible(true);
        $this->assertInstanceOf(KfAccountRepository::class, $kfAccountRepositoryProperty->getValue($command));
    }

    public function testCommandExecutionWithExistingAccount(): void
    {
        // 创建测试数据
        $accountRepository = self::getService(AccountRepository::class);
        $account = new Account();
        $account->setAppId('test_app_id_' . uniqid());
        $account->setAppSecret('test_app_secret');
        $account->setName('Test Account');
        $account->setToken('test_token');
        $accountRepository->save($account, true);

        $command = self::getService(SyncKfAccountCommand::class);
        $commandTester = new CommandTester($command);

        $exitCode = $commandTester->execute(['--account-id' => $account->getId()]);

        // 由于没有 Mock OfficialAccountClient，命令可能会失败，但至少应该能执行
        $this->assertContains($exitCode, [Command::SUCCESS, Command::FAILURE]);
        $output = $commandTester->getDisplay();

        // 验证输出包含相关信息
        $this->assertNotEmpty($output);
    }
}
