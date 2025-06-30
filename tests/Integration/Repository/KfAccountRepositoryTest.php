<?php

namespace WechatOfficialAccountCustomServiceBundle\Tests\Integration\Repository;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountCustomServiceBundle\Repository\KfAccountRepository;

class KfAccountRepositoryTest extends TestCase
{
    public function testRepositoryClassExists(): void
    {
        $this->assertTrue(class_exists(KfAccountRepository::class));
    }

    public function testRepositoryIsInstantiable(): void
    {
        $reflection = new \ReflectionClass(KfAccountRepository::class);
        $this->assertTrue($reflection->isInstantiable());
    }
}