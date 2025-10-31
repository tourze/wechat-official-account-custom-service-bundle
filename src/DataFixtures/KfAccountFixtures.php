<?php

namespace WechatOfficialAccountCustomServiceBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatOfficialAccountBundle\DataFixtures\AccountFixtures;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountCustomServiceBundle\Entity\KfAccount;
use WechatOfficialAccountCustomServiceBundle\Enum\KfAccountStatus;

#[When(env: 'test')]
#[When(env: 'dev')]
class KfAccountFixtures extends Fixture implements DependentFixtureInterface
{
    public const KF_ACCOUNT_DEMO_001 = 'kf-account-demo-001';
    public const KF_ACCOUNT_DEMO_002 = 'kf-account-demo-002';
    public const KF_ACCOUNT_DEMO_003 = 'kf-account-demo-003';

    public function load(ObjectManager $manager): void
    {
        $account = $this->getReference(AccountFixtures::ACCOUNT_REFERENCE, Account::class);

        $kfAccount1 = new KfAccount();
        $kfAccount1->setAccount($account);
        $kfAccount1->setKfAccount('demo_kf_001@test');
        $kfAccount1->setNickname('客服小王');
        $kfAccount1->setPassword('password123');
        $kfAccount1->setStatus(KfAccountStatus::ENABLED);
        $kfAccount1->setKfId('kf_id_001');
        $manager->persist($kfAccount1);

        $kfAccount2 = new KfAccount();
        $kfAccount2->setAccount($account);
        $kfAccount2->setKfAccount('demo_kf_002@test');
        $kfAccount2->setNickname('客服小李');
        $kfAccount2->setPassword('password456');
        $kfAccount2->setStatus(KfAccountStatus::DISABLED);
        $kfAccount2->setKfId('kf_id_002');
        $manager->persist($kfAccount2);

        $kfAccount3 = new KfAccount();
        $kfAccount3->setAccount($account);
        $kfAccount3->setKfAccount('demo_kf_003@test');
        $kfAccount3->setNickname('客服小张');
        $kfAccount3->setStatus(KfAccountStatus::ENABLED);
        $kfAccount3->setKfId('kf_id_003');
        $manager->persist($kfAccount3);

        $manager->flush();

        $this->addReference(self::KF_ACCOUNT_DEMO_001, $kfAccount1);
        $this->addReference(self::KF_ACCOUNT_DEMO_002, $kfAccount2);
        $this->addReference(self::KF_ACCOUNT_DEMO_003, $kfAccount3);
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
        ];
    }
}
