<?php

namespace Tourze\NES\MMC\Tests\Unit\Mapper;

use PHPUnit\Framework\TestCase;
use Tourze\NES\MMC\Mapper\MapperInterface;

/**
 * 测试映射器接口
 */
class MapperInterfaceTest extends TestCase
{
    /**
     * 使用Mock对象测试接口
     */
    public function testInterfaceContracts(): void
    {
        // 创建实现MapperInterface的匿名类
        $mockMapper = $this->getMockBuilder(MapperInterface::class)
            ->getMock();
        
        // 指定方法返回值
        $mockMapper->method('getId')->willReturn(123);
        $mockMapper->method('getMirroringMode')->willReturn(1); // 假设1是垂直镜像
        $mockMapper->method('irqState')->willReturn(false);
        
        // 测试实现接口的实例
        $this->assertInstanceOf(MapperInterface::class, $mockMapper);
        $this->assertEquals(123, $mockMapper->getId());
        $this->assertEquals(1, $mockMapper->getMirroringMode());
        $this->assertFalse($mockMapper->irqState());
    }
} 