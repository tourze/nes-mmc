<?php

namespace Tourze\NES\MMC\Tests\Unit\Mapper;

use PHPUnit\Framework\TestCase;
use Tourze\NES\Cartridge\Memory\ChrRom;
use Tourze\NES\Cartridge\Memory\PrgRom;
use Tourze\NES\Cartridge\Memory\SaveRam;
use Tourze\NES\MMC\Mapper\Mapper000;
use Tourze\NES\MMC\Mapper\MapperInterface;
use Tourze\NES\MMC\MirroringMode;

/**
 * 测试NROM (Mapper #0)
 */
class Mapper000Test extends TestCase
{
    /**
     * 准备16KB PRG ROM数据
     */
    private function preparePrgRom16kb(): PrgRom
    {
        // 创建一个16KB的PRG ROM，使每个地址的值对应其地址低字节
        $data = '';
        for ($i = 0; $i < 16 * 1024; $i++) {
            $data .= chr($i & 0xFF);
        }
        return new PrgRom($data);
    }
    
    /**
     * 准备32KB PRG ROM数据
     */
    private function preparePrgRom32kb(): PrgRom
    {
        // 创建一个32KB的PRG ROM，使每个地址的值对应其地址低字节
        $data = '';
        for ($i = 0; $i < 32 * 1024; $i++) {
            $data .= chr($i & 0xFF);
        }
        return new PrgRom($data);
    }
    
    /**
     * 准备8KB CHR ROM数据
     */
    private function prepareChrRom(): ChrRom
    {
        // 创建一个8KB的CHR ROM，使每个地址的值对应其地址低字节
        $data = '';
        for ($i = 0; $i < 8 * 1024; $i++) {
            $data .= chr($i & 0xFF);
        }
        return new ChrRom($data);
    }
    
    /**
     * 测试基本属性
     */
    public function testBasicProperties(): void
    {
        $prgRom = $this->preparePrgRom16kb();
        $chrRom = $this->prepareChrRom();
        $saveRam = new SaveRam(8 * 1024, false);
        
        $mapper = new Mapper000($prgRom, $chrRom, $saveRam, MirroringMode::VERTICAL);
        
        $this->assertInstanceOf(MapperInterface::class, $mapper);
        $this->assertSame(0, $mapper->getId());
        $this->assertSame(MirroringMode::VERTICAL, $mapper->getMirroringMode());
        $this->assertFalse($mapper->irqState());
    }
    
    /**
     * 测试16KB PRG ROM的CPU读取（需要镜像）
     */
    public function testCpuRead16kbPrgRom(): void
    {
        $prgRom = $this->preparePrgRom16kb();
        $chrRom = $this->prepareChrRom();
        $mapper = new Mapper000($prgRom, $chrRom);
        
        // 测试第一个16KB区域（0x8000-0xBFFF）
        for ($addr = 0x8000; $addr < 0xC000; $addr += 0x1000) {
            $expected = $addr & 0xFF; // 应该等于地址的低字节
            $this->assertSame($expected, $mapper->cpuRead($addr));
        }
        
        // 测试第二个16KB区域（0xC000-0xFFFF）—— 应镜像第一个16KB
        for ($addr = 0xC000; $addr < 0x10000; $addr += 0x1000) {
            $mirroredAddr = $addr - 0x4000; // 0xC000 -> 0x8000, 0xFFFF -> 0xBFFF
            $expected = $mirroredAddr & 0xFF;
            $this->assertSame($expected, $mapper->cpuRead($addr));
        }
    }
    
    /**
     * 测试32KB PRG ROM的CPU读取（不需要镜像）
     */
    public function testCpuRead32kbPrgRom(): void
    {
        $prgRom = $this->preparePrgRom32kb();
        $chrRom = $this->prepareChrRom();
        $mapper = new Mapper000($prgRom, $chrRom);
        
        // 测试第一个16KB区域（0x8000-0xBFFF）
        for ($addr = 0x8000; $addr < 0xC000; $addr += 0x1000) {
            $expected = $addr & 0xFF;
            $this->assertSame($expected, $mapper->cpuRead($addr));
        }
        
        // 测试第二个16KB区域（0xC000-0xFFFF）—— 不镜像，直接读取32KB ROM的后16KB
        for ($addr = 0xC000; $addr < 0x10000; $addr += 0x1000) {
            $expected = $addr & 0xFF;
            $this->assertSame($expected, $mapper->cpuRead($addr));
        }
    }
    
    /**
     * 测试SRAM区域的CPU读写
     */
    public function testCpuReadWriteSram(): void
    {
        $prgRom = $this->preparePrgRom16kb();
        $chrRom = $this->prepareChrRom();
        $saveRam = new SaveRam(8 * 1024, false);
        $mapper = new Mapper000($prgRom, $chrRom, $saveRam);
        
        // 测试SRAM区域（0x6000-0x7FFF）
        $testAddr = 0x6000;
        $testData = 0x42;
        
        // 写入数据
        $mapper->cpuWrite($testAddr, $testData);
        
        // 读取并验证
        $this->assertSame($testData, $mapper->cpuRead($testAddr));
        
        // 测试边界
        $testAddrEnd = 0x7FFF;
        $testDataEnd = 0x43;
        
        $mapper->cpuWrite($testAddrEnd, $testDataEnd);
        $this->assertSame($testDataEnd, $mapper->cpuRead($testAddrEnd));
    }
    
    /**
     * 测试PPU读写
     */
    public function testPpuReadWrite(): void
    {
        $prgRom = $this->preparePrgRom16kb();
        $chrRom = $this->prepareChrRom();
        $mapper = new Mapper000($prgRom, $chrRom);
        
        // 测试图案表区域（0x0000-0x1FFF）
        for ($addr = 0x0000; $addr < 0x2000; $addr += 0x0800) {
            $expected = $addr & 0xFF;
            $this->assertSame($expected, $mapper->ppuRead($addr));
        }
        
        // 超出范围的地址应该返回0
        $this->assertSame(0, $mapper->ppuRead(0x2000));
        $this->assertSame(0, $mapper->ppuRead(0x3000));
    }
    
    /**
     * 测试CHR RAM模式（CHR ROM为空时）
     */
    public function testChrRamMode(): void
    {
        $prgRom = $this->preparePrgRom16kb();
        // 创建一个非空CHR ROM，即大小为8KB的内存
        $chrRom = new ChrRom(str_repeat("\x00", 8 * 1024));
        $mapper = new Mapper000($prgRom, $chrRom);
        
        // 测试写入CHR RAM
        $addr = 0x1000;
        $data = 0x42;
        
        $mapper->ppuWrite($addr, $data);
        $this->assertSame($data, $mapper->ppuRead($addr));
    }
} 