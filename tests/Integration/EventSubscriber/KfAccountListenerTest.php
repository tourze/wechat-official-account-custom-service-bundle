<?php

namespace WechatOfficialAccountCustomServiceBundle\Tests\Integration\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountCustomServiceBundle\Entity\KfAccount;
use WechatOfficialAccountCustomServiceBundle\EventSubscriber\KfAccountListener;

class KfAccountListenerTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $client = $this->createMock(OfficialAccountClient::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $listener = new KfAccountListener($client, $logger);
        
        $this->assertInstanceOf(KfAccountListener::class, $listener);
    }

    public function testPostPersistWithSyncingAccount(): void
    {
        $client = $this->createMock(OfficialAccountClient::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $listener = new KfAccountListener($client, $logger);
        $account = $this->createMock(KfAccount::class);
        $account->method('isSyncing')->willReturn(true);
        
        $client->expects($this->never())->method('request');
        
        $listener->postPersist($account);
    }

    public function testPreRemoveWithSyncingAccount(): void
    {
        $client = $this->createMock(OfficialAccountClient::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $listener = new KfAccountListener($client, $logger);
        $account = $this->createMock(KfAccount::class);
        $account->method('isSyncing')->willReturn(true);
        
        $client->expects($this->never())->method('request');
        
        $listener->preRemove($account);
    }
}