<?php

namespace WechatOfficialAccountCustomServiceBundle\Tests\Repository;

use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountCustomServiceBundle\Entity\KfAccount;
use WechatOfficialAccountCustomServiceBundle\Enum\KfAccountStatus;
use WechatOfficialAccountCustomServiceBundle\Repository\KfAccountRepository;

/**
 * @template TEntity of KfAccount
 * @extends AbstractRepositoryTestCase<TEntity>
 * @internal
 */
#[CoversClass(KfAccountRepository::class)]
#[RunTestsInSeparateProcesses]
final class KfAccountRepositoryTest extends AbstractRepositoryTestCase
{
    private KfAccountRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(KfAccountRepository::class);

        // 如果当前测试是数据库连接测试，跳过数据加载操作
        if ($this->isTestingDatabaseConnection()) {
            return;
        }

        // 清理实体管理器状态，避免影响数据库连接测试
        try {
            self::getEntityManager()->clear();
        } catch (\Exception $e) {
            // 忽略清理错误
        }
    }

    public function testFindOneByWithExistingCriteriaShouldReturnEntity(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount = $this->createKfAccountFixture($account);
        $this->repository->save($kfAccount, true);

        $result = $this->repository->findOneBy(['kfAccount' => $kfAccount->getKfAccount()]);

        $this->assertInstanceOf(KfAccount::class, $result);
        $this->assertEquals($kfAccount->getKfAccount(), $result->getKfAccount());
    }

    public function testFindOneByWithNonExistentCriteriaShouldReturnNull(): void
    {
        $result = $this->repository->findOneBy(['kfAccount' => 'nonexistent@account']);

        $this->assertNull($result);
    }

    public function testFindOneByWithEmptyCriteriaShouldReturnFirstEntity(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount = $this->createKfAccountFixture($account);
        $this->repository->save($kfAccount, true);

        $result = $this->repository->findOneBy([]);

        $this->assertInstanceOf(KfAccount::class, $result);
    }

    public function testFindOneByWithNullValueShouldFindNullField(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount = $this->createKfAccountFixture($account);
        $kfAccount->setPassword(null);
        $this->repository->save($kfAccount, true);

        $result = $this->repository->findOneBy(['password' => null]);

        $this->assertInstanceOf(KfAccount::class, $result);
        $this->assertNull($result->getPassword());
    }

    public function testFindAllShouldReturnArray(): void
    {
        $results = $this->repository->findAll();

        $this->assertNotEmpty($results);
    }

    public function testFindByShouldReturnArray(): void
    {
        $results = $this->repository->findBy(['status' => KfAccountStatus::ENABLED]);

        $this->assertNotEmpty($results);
        foreach ($results as $result) {
            $this->assertEquals(KfAccountStatus::ENABLED, $result->getStatus());
        }
    }

    public function testFindByWithLimitShouldLimitResults(): void
    {
        $results = $this->repository->findBy([], null, 1);

        $this->assertLessThanOrEqual(1, count($results));
    }

    public function testFindByWithOffsetShouldSkipResults(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount1 = $this->createKfAccountFixture($account, 'test1@account');
        $kfAccount2 = $this->createKfAccountFixture($account, 'test2@account');
        $this->repository->save($kfAccount1, false);
        $this->repository->save($kfAccount2, true);

        $allResults = $this->repository->findBy([], ['id' => 'ASC']);
        $offsetResults = $this->repository->findBy([], ['id' => 'ASC'], null, 1);

        $this->assertGreaterThan(1, count($allResults), 'Should have at least 2 records for offset test');
        $this->assertNotEquals($allResults[0]->getId(), $offsetResults[0]->getId());
    }

    public function testCountWithNullValueShouldCountNullFields(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount = $this->createKfAccountFixture($account);
        $kfAccount->setPassword(null);
        $this->repository->save($kfAccount, true);

        $count = $this->repository->count(['password' => null]);

        $this->assertGreaterThan(0, $count);
    }

    public function testCountWithAssociationShouldWork(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount = $this->createKfAccountFixture($account);
        $this->repository->save($kfAccount, true);

        $count = $this->repository->count(['account' => $account]);

        $this->assertGreaterThan(0, $count);
    }

    public function testFindByWithAssociationShouldWork(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount = $this->createKfAccountFixture($account);
        $this->repository->save($kfAccount, true);

        $results = $this->repository->findBy(['account' => $account]);

        $this->assertNotEmpty($results);
        foreach ($results as $result) {
            $this->assertEquals($account->getId(), $result->getAccount()->getId());
        }
    }

    public function testSaveShouldPersistEntity(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount = $this->createKfAccountFixture($account);

        $this->repository->save($kfAccount, true);

        $this->assertNotNull($kfAccount->getId());
        $this->assertInstanceOf(KfAccount::class, $this->repository->find($kfAccount->getId()));
    }

    public function testSaveWithoutFlushShouldNotPersistImmediately(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount = $this->createKfAccountFixture($account);
        $kfAccountToCheck = $kfAccount->getKfAccount();

        $this->repository->save($kfAccount, false);

        $this->assertNull($this->repository->findOneByKfAccount($kfAccountToCheck), 'Entity should not be findable in database before flush');
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount = $this->createKfAccountFixture($account);
        $this->repository->save($kfAccount, true);
        $id = $kfAccount->getId();

        $this->repository->remove($kfAccount, true);

        $this->assertNull($this->repository->find($id));
    }

    public function testRemoveWithoutFlushShouldNotDeleteImmediately(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount = $this->createKfAccountFixture($account);
        $this->repository->save($kfAccount, true);
        $id = $kfAccount->getId();

        $this->repository->remove($kfAccount, false);

        $this->assertInstanceOf(KfAccount::class, $this->repository->find($id));
    }

    public function testFindAllEnabledShouldReturnEnabledAccounts(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount = $this->createKfAccountFixture($account);
        $kfAccount->setStatus(KfAccountStatus::ENABLED);
        $this->repository->save($kfAccount, true);

        $results = $this->repository->findAllEnabled();

        $this->assertNotEmpty($results);
        foreach ($results as $result) {
            $this->assertEquals(KfAccountStatus::ENABLED, $result->getStatus());
        }
    }

    public function testFindOneByKfAccountWithExistingAccountShouldReturnEntity(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount = $this->createKfAccountFixture($account);
        $this->repository->save($kfAccount, true);

        $result = $this->repository->findOneByKfAccount($kfAccount->getKfAccount());

        $this->assertInstanceOf(KfAccount::class, $result);
        $this->assertEquals($kfAccount->getKfAccount(), $result->getKfAccount());
    }

    public function testFindOneByKfAccountWithNonExistentAccountShouldReturnNull(): void
    {
        $result = $this->repository->findOneByKfAccount('nonexistent@account');

        $this->assertNull($result);
    }

    public function testCountGroupByStatusShouldReturnStatusCounts(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount = $this->createKfAccountFixture($account);
        $kfAccount->setStatus(KfAccountStatus::ENABLED);
        $this->repository->save($kfAccount, true);

        $counts = $this->repository->countGroupByStatus();

        $this->assertArrayHasKey(KfAccountStatus::ENABLED->value, $counts);
        $this->assertGreaterThanOrEqual(0, $counts[KfAccountStatus::ENABLED->value]);
    }

    public function testGetQueryBuilderShouldReturnQueryBuilder(): void
    {
        $qb = $this->repository->getQueryBuilder();

        $this->assertInstanceOf(QueryBuilder::class, $qb);
    }

    public function testFindOneByWithOrderByShouldRespectOrder(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount1 = $this->createKfAccountFixture($account, 'a@account');
        $kfAccount2 = $this->createKfAccountFixture($account, 'z@account');
        $this->repository->save($kfAccount1, false);
        $this->repository->save($kfAccount2, true);

        $result = $this->repository->findOneBy([], ['kfAccount' => 'ASC']);

        $this->assertInstanceOf(KfAccount::class, $result);
    }

    public function testCountWithNullFieldQueryShouldWork(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount = $this->createKfAccountFixture($account);
        $kfAccount->setAvatar(null);
        $this->repository->save($kfAccount, true);

        $count = $this->repository->count(['avatar' => null]);

        $this->assertGreaterThan(0, $count);
    }

    public function testCountWithKfIdNullShouldWork(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount = $this->createKfAccountFixture($account);
        $kfAccount->setKfId(null);
        $this->repository->save($kfAccount, true);

        $count = $this->repository->count(['kfId' => null]);

        $this->assertGreaterThan(0, $count);
    }

    private function isTestingDatabaseConnection(): bool
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        foreach ($backtrace as $trace) {
            if (str_contains($trace['function'], 'testFindWhenDatabaseIsUnavailable')) {
                return true;
            }
            if (str_contains($trace['function'], 'testFindByWhenDatabaseIsUnavailable')) {
                return true;
            }
            if (str_contains($trace['function'], 'testFindAllWhenDatabaseIsUnavailable')) {
                return true;
            }
            if (str_contains($trace['function'], 'testCountWhenDatabaseIsUnavailable')) {
                return true;
            }
        }

        return false;
    }

    private function createAccountFixture(): Account
    {
        $account = new Account();
        $account->setAppId('test_app_id_' . uniqid());
        $account->setAppSecret('test_app_secret');
        $account->setName('Test Account');
        $account->setToken('test_token');

        // 只在非数据库连接测试时持久化
        if (!$this->isTestingDatabaseConnection()) {
            $accountRepository = self::getService('WechatOfficialAccountBundle\Repository\AccountRepository');
            $accountRepository->save($account, true);
        }

        return $account;
    }

    private function createKfAccountFixture(Account $account, ?string $kfAccount = null): KfAccount
    {
        $kfAccountEntity = new KfAccount();
        $kfAccountEntity->setAccount($account);
        $kfAccountEntity->setKfAccount($kfAccount ?? 'test@account_' . uniqid());
        $kfAccountEntity->setNickname('Test KF Account');
        $kfAccountEntity->setStatus(KfAccountStatus::ENABLED);

        return $kfAccountEntity;
    }

    public function testFindOneByWithOrderBySortingLogic(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount1 = $this->createKfAccountFixture($account, 'a@account');
        $kfAccount2 = $this->createKfAccountFixture($account, 'z@account');
        $this->repository->save($kfAccount1, false);
        $this->repository->save($kfAccount2, true);

        $resultAsc = $this->repository->findOneBy([], ['kfAccount' => 'ASC']);
        $resultDesc = $this->repository->findOneBy([], ['kfAccount' => 'DESC']);

        $this->assertInstanceOf(KfAccount::class, $resultAsc);
        $this->assertInstanceOf(KfAccount::class, $resultDesc);
        $this->assertEquals('a@account', $resultAsc->getKfAccount());
        $this->assertEquals('z@account', $resultDesc->getKfAccount());
    }

    public function testCountWithAssociatedEntityQuery(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount = $this->createKfAccountFixture($account);
        $this->repository->save($kfAccount, true);

        $count = $this->repository->count(['account' => $account]);

        $this->assertGreaterThan(0, $count);
    }

    public function testFindByWithAssociatedEntityQuery(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount = $this->createKfAccountFixture($account);
        $this->repository->save($kfAccount, true);

        $results = $this->repository->findBy(['account' => $account]);

        $this->assertNotEmpty($results);
        foreach ($results as $result) {
            $this->assertEquals($account->getId(), $result->getAccount()->getId());
        }
    }

    public function testFindOneByWithAssociatedEntityQuery(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount = $this->createKfAccountFixture($account);
        $this->repository->save($kfAccount, true);

        $result = $this->repository->findOneBy(['account' => $account]);

        $this->assertInstanceOf(KfAccount::class, $result);
        $this->assertEquals($account->getId(), $result->getAccount()->getId());
    }

    public function testCountWithNullPasswordQuery(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount = $this->createKfAccountFixture($account);
        $kfAccount->setPassword(null);
        $this->repository->save($kfAccount, true);

        $count = $this->repository->count(['password' => null]);

        $this->assertGreaterThan(0, $count);
    }

    public function testFindByWithNullPasswordQuery(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount = $this->createKfAccountFixture($account);
        $kfAccount->setPassword(null);
        $this->repository->save($kfAccount, true);

        $results = $this->repository->findBy(['password' => null]);

        $this->assertNotEmpty($results);
        foreach ($results as $result) {
            $this->assertNull($result->getPassword());
        }
    }

    protected function createNewEntity(): object
    {
        // 在数据库连接测试中，创建简单的实体对象但不依赖数据库操作
        if ($this->isTestingDatabaseConnection()) {
            $account = new Account();
            $account->setAppId('test_app_id');
            $account->setAppSecret('test_app_secret');
            $account->setName('Test Account');
            $account->setToken('test_token');

            $kfAccountEntity = new KfAccount();
            $kfAccountEntity->setAccount($account);
            $kfAccountEntity->setKfAccount('test@account');
            $kfAccountEntity->setNickname('Test KF Account');
            $kfAccountEntity->setStatus(KfAccountStatus::ENABLED);

            return $kfAccountEntity;
        }

        $account = $this->createAccountFixture();

        return $this->createKfAccountFixture($account);
    }

    /** @return KfAccountRepository */
    protected function getRepository(): KfAccountRepository
    {
        return $this->repository;
    }

    // ========== PHPStan 要求的额外测试方法 ==========

    public function testFindOneByAssociationAccountShouldReturnMatchingEntity(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount = $this->createKfAccountFixture($account);
        $this->repository->save($kfAccount, true);

        $result = $this->repository->findOneBy(['account' => $account]);

        $this->assertNotNull($result);
        $this->assertInstanceOf(KfAccount::class, $result);
        $this->assertEquals($account->getId(), $result->getAccount()->getId());
    }

    public function testCountWithAssociationAccountShouldReturnCorrectCount(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount = $this->createKfAccountFixture($account);
        $this->repository->save($kfAccount, true);

        $count = $this->repository->count(['account' => $account]);

        $this->assertGreaterThan(0, $count);
    }

    public function testCountWithPasswordAsNullShouldReturnCorrectCount(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount = $this->createKfAccountFixture($account);
        $kfAccount->setPassword(null);
        $this->repository->save($kfAccount, true);

        $count = $this->repository->count(['password' => null]);

        $this->assertGreaterThan(0, $count);
    }

    public function testCountWithAvatarAsNullShouldReturnCorrectCount(): void
    {
        $account = $this->createAccountFixture();
        $kfAccount = $this->createKfAccountFixture($account);
        $kfAccount->setAvatar(null);
        $this->repository->save($kfAccount, true);

        $count = $this->repository->count(['avatar' => null]);

        $this->assertGreaterThan(0, $count);
    }

    // ========== Count 规则要求的方法 ==========

    public function testCountByAssociationAccountShouldReturnCorrectNumber(): void
    {
        $account1 = $this->createAccountFixture();
        $account2 = $this->createAccountFixture();

        // 创建 4 个属于 account1 的实体
        for ($i = 0; $i < 4; ++$i) {
            $kfAccount = $this->createKfAccountFixture($account1);
            $this->repository->save($kfAccount, false);
        }

        // 创建 2 个属于 account2 的实体
        for ($i = 0; $i < 2; ++$i) {
            $kfAccount = $this->createKfAccountFixture($account2);
            $this->repository->save($kfAccount, false);
        }

        $this->repository->save($this->createKfAccountFixture($account1), true); // 触发 flush

        $count = $this->repository->count(['account' => $account1]);
        $this->assertSame(5, $count); // 4 + 1 = 5
    }

    // ========== FindBy 规则要求的方法 ==========
}
