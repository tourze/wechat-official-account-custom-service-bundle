<?php

namespace WechatOfficialAccountCustomServiceBundle\Tests\Integration\Command;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountCustomServiceBundle\Command\SyncKfAccountCommand;

class SyncKfAccountCommandTest extends TestCase
{
    public function testCommandHasCorrectName(): void
    {
        $this->assertEquals('wechat-official-account:custom-service:sync-account-list', SyncKfAccountCommand::NAME);
    }
}