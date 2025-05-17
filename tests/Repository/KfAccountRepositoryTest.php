<?php

namespace WechatOfficialAccountCustomServiceBundle\Tests\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use WechatOfficialAccountCustomServiceBundle\Entity\KfAccount;
use WechatOfficialAccountCustomServiceBundle\Enum\KfAccountStatus;
use WechatOfficialAccountCustomServiceBundle\Repository\KfAccountRepository;

class KfAccountRepositoryTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private ManagerRegistry $registry;
    
    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->registry = $this->createMock(ManagerRegistry::class);
        
        $this->registry->method('getManagerForClass')
            ->willReturn($this->entityManager);
    }
    
    private function createMockRepository()
    {
        $repository = $this->getMockBuilder(KfAccountRepository::class)
            ->setConstructorArgs([$this->registry])
            ->onlyMethods(['createQueryBuilder'])
            ->getMock();
            
        return $repository;
    }
    
    public function testFindAllEnabled_returnsEnabledAccounts(): void
    {
        $repository = $this->createMockRepository();
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(Query::class);
        
        $mockResult = [new KfAccount(), new KfAccount()];
        
        $repository->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);
            
        $queryBuilder->expects($this->any())
            ->method('select')
            ->willReturnSelf();
            
        $queryBuilder->expects($this->any())
            ->method('from')
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('andWhere')
            ->with('k.status = :status')
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('setParameter')
            ->with('status', KfAccountStatus::ENABLED)
            ->willReturnSelf();
            
        $queryBuilder->expects($this->any())
            ->method('orderBy')
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);
            
        $query->expects($this->once())
            ->method('getResult')
            ->willReturn($mockResult);
        
        $result = $repository->findAllEnabled();
        
        $this->assertSame($mockResult, $result);
    }
    
    public function testFindOneByKfAccount_returnsMatchingAccount(): void
    {
        $repository = $this->createMockRepository();
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(Query::class);
        $kfAccount = new KfAccount();
        
        $repository->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);
            
        $queryBuilder->expects($this->any())
            ->method('select')
            ->willReturnSelf();
            
        $queryBuilder->expects($this->any())
            ->method('from')
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('andWhere')
            ->with('k.kfAccount = :kfAccount')
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('setParameter')
            ->with('kfAccount', 'test_kf@account')
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);
            
        $query->expects($this->once())
            ->method('getOneOrNullResult')
            ->willReturn($kfAccount);
        
        $result = $repository->findOneByKfAccount('test_kf@account');
        
        $this->assertSame($kfAccount, $result);
    }
    
    public function testFindOneByKfAccount_returnsNull_whenNoAccountFound(): void
    {
        $repository = $this->createMockRepository();
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(Query::class);
        
        $repository->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);
            
        $queryBuilder->expects($this->any())
            ->method('select')
            ->willReturnSelf();
            
        $queryBuilder->expects($this->any())
            ->method('from')
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('andWhere')
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('setParameter')
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);
            
        $query->expects($this->once())
            ->method('getOneOrNullResult')
            ->willReturn(null);
        
        $result = $repository->findOneByKfAccount('nonexistent@account');
        
        $this->assertNull($result);
    }
    
    public function testGetQueryBuilder_returnsQueryBuilder(): void
    {
        $repository = $this->createMockRepository();
        $queryBuilder = $this->createMock(QueryBuilder::class);
        
        $repository->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);
            
        $queryBuilder->expects($this->any())
            ->method('select')
            ->willReturnSelf();
            
        $queryBuilder->expects($this->any())
            ->method('from')
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('orderBy')
            ->with('k.createdAt', 'DESC')
            ->willReturnSelf();
        
        $result = $repository->getQueryBuilder();
        
        $this->assertSame($queryBuilder, $result);
    }
    
    public function testCountGroupByStatus_returnsFormattedResults(): void
    {
        $repository = $this->createMockRepository();
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(Query::class);
        
        $queryResult = [
            ['status' => KfAccountStatus::ENABLED, 'count' => '10'],
            ['status' => KfAccountStatus::DISABLED, 'count' => '5'],
            ['status' => KfAccountStatus::DELETED, 'count' => '2']
        ];
        
        $repository->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);
            
        $queryBuilder->expects($this->once())
            ->method('select')
            ->with('k.status', 'COUNT(k.id) as count')
            ->willReturnSelf();
            
        $queryBuilder->expects($this->any())
            ->method('from')
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('groupBy')
            ->with('k.status')
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);
            
        $query->expects($this->once())
            ->method('getResult')
            ->willReturn($queryResult);
        
        $result = $repository->countGroupByStatus();
        
        $expected = [
            'enabled' => 10,
            'disabled' => 5,
            'deleted' => 2
        ];
        
        $this->assertEquals($expected, $result);
    }
} 