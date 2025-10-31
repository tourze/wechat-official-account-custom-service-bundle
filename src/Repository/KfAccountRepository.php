<?php

namespace WechatOfficialAccountCustomServiceBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use WechatOfficialAccountCustomServiceBundle\Entity\KfAccount;
use WechatOfficialAccountCustomServiceBundle\Enum\KfAccountStatus;

/**
 * @extends ServiceEntityRepository<KfAccount>
 */
#[AsRepository(entityClass: KfAccount::class)]
class KfAccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, KfAccount::class);
    }

    /**
     * 查找所有启用的客服账号
     *
     * @return KfAccount[]
     */
    public function findAllEnabled(): array
    {
        /** @var KfAccount[] */
        return $this->createQueryBuilder('k')
            ->andWhere('k.status = :status')
            ->setParameter('status', KfAccountStatus::ENABLED)
            ->orderBy('k.updateTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * 根据客服账号查找
     */
    public function findOneByKfAccount(string $kfAccount): ?KfAccount
    {
        /** @var KfAccount|null */
        return $this->createQueryBuilder('k')
            ->andWhere('k.kfAccount = :kfAccount')
            ->setParameter('kfAccount', $kfAccount)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * 获取查询构建器
     */
    public function getQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('k')
            ->orderBy('k.updateTime', 'DESC')
        ;
    }

    /**
     * 统计各状态的客服账号数量
     *
     * @return array<string, int>
     */
    public function countGroupByStatus(): array
    {
        /** @var array<array{status: KfAccountStatus, count: int|string}> $result */
        $result = $this->createQueryBuilder('k')
            ->select('k.status', 'COUNT(k.id) as count')
            ->groupBy('k.status')
            ->getQuery()
            ->getResult()
        ;

        $counts = [];
        foreach ($result as $row) {
            $counts[$row['status']->value] = (int) $row['count'];
        }

        return $counts;
    }

    public function save(KfAccount $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(KfAccount $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
