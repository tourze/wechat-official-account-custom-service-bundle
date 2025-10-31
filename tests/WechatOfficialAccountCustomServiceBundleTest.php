<?php

declare(strict_types=1);

namespace WechatOfficialAccountCustomServiceBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use WechatOfficialAccountCustomServiceBundle\WechatOfficialAccountCustomServiceBundle;

/**
 * @internal
 */
#[CoversClass(WechatOfficialAccountCustomServiceBundle::class)]
#[RunTestsInSeparateProcesses]
final class WechatOfficialAccountCustomServiceBundleTest extends AbstractBundleTestCase
{
}
