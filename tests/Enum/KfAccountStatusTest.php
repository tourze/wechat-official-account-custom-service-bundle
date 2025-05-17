<?php

namespace WechatOfficialAccountCustomServiceBundle\Tests\Enum;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountCustomServiceBundle\Enum\KfAccountStatus;

class KfAccountStatusTest extends TestCase
{
    public function testGetLabel_forAllCases(): void
    {
        $this->assertSame('启用', KfAccountStatus::ENABLED->getLabel());
        $this->assertSame('禁用', KfAccountStatus::DISABLED->getLabel());
        $this->assertSame('已删除', KfAccountStatus::DELETED->getLabel());
    }
    
    public function testCases_returnsAllEnumValues(): void
    {
        $cases = KfAccountStatus::cases();
        
        $this->assertCount(3, $cases);
        $this->assertContains(KfAccountStatus::ENABLED, $cases);
        $this->assertContains(KfAccountStatus::DISABLED, $cases);
        $this->assertContains(KfAccountStatus::DELETED, $cases);
    }
    
    public function testGenOptions_returnsCorrectStructure(): void
    {
        $options = KfAccountStatus::genOptions();
        
        $this->assertIsArray($options);
        $this->assertCount(3, $options);
        
        foreach ($options as $item) {
            $this->assertArrayHasKey('value', $item);
            $this->assertArrayHasKey('label', $item);
            $this->assertArrayHasKey('text', $item);
            $this->assertArrayHasKey('name', $item);
            
            $this->assertContains($item['value'], ['enabled', 'disabled', 'deleted']);
            $this->assertContains($item['label'], ['启用', '禁用', '已删除']);
        }
        
        // 验证特定条目
        $enabledItem = array_filter($options, fn($item) => $item['value'] === 'enabled');
        $this->assertCount(1, $enabledItem);
        $enabledItem = reset($enabledItem);
        $this->assertSame('启用', $enabledItem['label']);
    }
    
    public function testGenOptions_returnsOptionsInCorrectOrder(): void
    {
        $options = KfAccountStatus::genOptions();
        
        $values = array_column($options, 'value');
        
        // 测试顺序与枚举定义顺序一致
        $this->assertSame('enabled', $values[0]);
        $this->assertSame('disabled', $values[1]);
        $this->assertSame('deleted', $values[2]);
    }
    
    public function testToSelectItem_returnsCorrectStructure(): void
    {
        $item = KfAccountStatus::ENABLED->toSelectItem();
        
        $this->assertIsArray($item);
        $this->assertArrayHasKey('value', $item);
        $this->assertArrayHasKey('label', $item);
        $this->assertArrayHasKey('text', $item);
        $this->assertArrayHasKey('name', $item);
        
        $this->assertSame('enabled', $item['value']);
        $this->assertSame('启用', $item['label']);
        $this->assertSame('启用', $item['text']);
        $this->assertSame('启用', $item['name']);
    }
    
    public function testToArray_returnsCorrectStructure(): void
    {
        $array = KfAccountStatus::DISABLED->toArray();
        
        $this->assertIsArray($array);
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        
        $this->assertSame('disabled', $array['value']);
        $this->assertSame('禁用', $array['label']);
    }
} 