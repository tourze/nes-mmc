<?php

namespace Tourze\NES\MMC\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tourze\NES\Cartridge\Memory\ChrRom;
use Tourze\NES\Cartridge\Memory\PrgRom;
use Tourze\NES\Cartridge\Memory\SaveRam;
use Tourze\NES\MMC\Exception\UnsupportedFeatureException;
use Tourze\NES\MMC\Mapper\Mapper000;
use Tourze\NES\MMC\Mapper\MapperInterface;
use Tourze\NES\MMC\MapperFactory;
use Tourze\NES\MMC\MirroringMode;
use Tourze\NES\MMC\Registry\MapperRegistry;

/**
 * 测试映射器工厂
 */
class MapperFactoryTest extends TestCase
{
    /**
     * 测试前的准备
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // 确保测试前注册表是空的
        MapperRegistry::reset();
        
        // 注册测试用的映射器
        MapperRegistry::register(0, Mapper000::class);
    }
    
    /**
     * 测试创建映射器实例
     */
    public function testCreateMapper(): void
    {
        $prgRom = new PrgRom(str_repeat("\x00", 16 * 1024));
        $chrRom = new ChrRom(str_repeat("\x00", 8 * 1024));
        $saveRam = new SaveRam(8 * 1024, false);
        
        $mapper = MapperFactory::create(0, $prgRom, $chrRom, $saveRam, MirroringMode::VERTICAL);
        
        $this->assertInstanceOf(MapperInterface::class, $mapper);
        $this->assertInstanceOf(Mapper000::class, $mapper);
        $this->assertSame(0, $mapper->getId());
        $this->assertSame(MirroringMode::VERTICAL, $mapper->getMirroringMode());
    }
    
    /**
     * 测试创建不支持的映射器抛出异常
     */
    public function testCreateUnsupportedMapperThrowsException(): void
    {
        $prgRom = new PrgRom(str_repeat("\x00", 16 * 1024));
        $chrRom = new ChrRom(str_repeat("\x00", 8 * 1024));
        
        $this->expectException(UnsupportedFeatureException::class);
        $this->expectExceptionMessage('不支持的映射器类型：999');
        
        MapperFactory::create(999, $prgRom, $chrRom);
    }
    
    /**
     * 测试使用默认参数创建映射器
     */
    public function testCreateMapperWithDefaults(): void
    {
        $prgRom = new PrgRom(str_repeat("\x00", 16 * 1024));
        $chrRom = new ChrRom(str_repeat("\x00", 8 * 1024));
        
        $mapper = MapperFactory::create(0, $prgRom, $chrRom);
        
        $this->assertInstanceOf(Mapper000::class, $mapper);
        $this->assertSame(MirroringMode::HORIZONTAL, $mapper->getMirroringMode());
    }
} 