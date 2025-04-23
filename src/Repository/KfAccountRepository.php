<?php

namespace WechatOfficialAccountCustomServiceBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use WechatOfficialAccountCustomServiceBundle\Entity\KfAccount;
use WechatOfficialAccountCustomServiceBundle\Enum\KfAccountStatus;

/**
 * @method KfAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method KfAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method KfAccount[]    findAll()
 * @method KfAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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
        return $this->createQueryBuilder('k')
            ->andWhere('k.status = :status')
            ->setParameter('status', KfAccountStatus::ENABLED)
            ->orderBy('k.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 根据客服账号查找
     */
    public function findOneByKfAccount(string $kfAccount): ?KfAccount
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.kfAccount = :kfAccount')
            ->setParameter('kfAccount', $kfAccount)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * 获取查询构建器
     */
    public function getQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('k')
            ->orderBy('k.createdAt', 'DESC');
    }

    /**
     * 统计各状态的客服账号数量
     *
     * @return array<string, int>
     */
    public function countGroupByStatus(): array
    {
        $result = $this->createQueryBuilder('k')
            ->select('k.status', 'COUNT(k.id) as count')
            ->groupBy('k.status')
            ->getQuery()
            ->getResult();

        $counts = [];
        foreach ($result as $row) {
            $counts[$row['status']->value] = (int) $row['count'];
        }

        return $counts;
    }
}
