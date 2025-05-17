<?php

namespace Tourze\NES\MMC\Tests\Unit\Registry;

use PHPUnit\Framework\TestCase;
use Tourze\NES\MMC\Exception\UnsupportedFeatureException;
use Tourze\NES\MMC\Mapper\Mapper000;
use Tourze\NES\MMC\Registry\MapperRegistry;

/**
 * 测试映射器注册表
 */
class MapperRegistryTest extends TestCase
{
    /**
     * 在每个测试后重置注册表
     */
    protected function tearDown(): void
    {
        // 重置注册表以避免测试之间的干扰
        MapperRegistry::reset();
        parent::tearDown();
    }

    /**
     * 测试映射器注册和获取
     */
    public function testRegisterAndGetMapperClass(): void
    {
        // 注册映射器
        MapperRegistry::register(0, Mapper000::class);

        // 获取映射器类
        $mapperClass = MapperRegistry::getMapperClass(0);

        $this->assertSame(Mapper000::class, $mapperClass);
    }

    /**
     * 测试获取未注册的映射器抛出异常
     */
    public function testGetUnregisteredMapperThrowsException(): void
    {
        $this->expectException(UnsupportedFeatureException::class);
        $this->expectExceptionMessage('不支持的映射器类型：999');

        MapperRegistry::getMapperClass(999);
    }

    /**
     * 测试判断映射器是否支持
     */
    public function testIsMapperSupported(): void
    {
        MapperRegistry::register(0, Mapper000::class);

        $this->assertTrue(MapperRegistry::isMapperSupported(0));
        $this->assertFalse(MapperRegistry::isMapperSupported(123));
    }

    /**
     * 测试重置注册表
     */
    public function testReset(): void
    {
        // 注册映射器
        MapperRegistry::register(0, Mapper000::class);
        $this->assertTrue(MapperRegistry::isMapperSupported(0));

        // 重置注册表
        MapperRegistry::reset();

        // 应该不再支持任何映射器
        $this->assertFalse(MapperRegistry::isMapperSupported(0));
    }

    /**
     * 测试注册多个映射器
     */
    public function testRegisterMultipleMappers(): void
    {
        MapperRegistry::register(0, Mapper000::class);
        MapperRegistry::register(1, 'CustomMapper1');
        MapperRegistry::register(2, 'CustomMapper2');

        $this->assertTrue(MapperRegistry::isMapperSupported(0));
        $this->assertTrue(MapperRegistry::isMapperSupported(1));
        $this->assertTrue(MapperRegistry::isMapperSupported(2));
        $this->assertFalse(MapperRegistry::isMapperSupported(3));

        $this->assertSame('CustomMapper1', MapperRegistry::getMapperClass(1));
        $this->assertSame('CustomMapper2', MapperRegistry::getMapperClass(2));
    }
}
