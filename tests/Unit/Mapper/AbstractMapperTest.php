<?php

namespace Tourze\NES\MMC\Tests\Unit\Mapper;

use PHPUnit\Framework\TestCase;
use Tourze\NES\Cartridge\Memory\ChrRom;
use Tourze\NES\Cartridge\Memory\PrgRom;
use Tourze\NES\Cartridge\Memory\SaveRam;
use Tourze\NES\MMC\Mapper\AbstractMapper;
use Tourze\NES\MMC\MirroringMode;

/**
 * 测试映射器抽象基类
 */
class AbstractMapperTest extends TestCase
{
    /**
     * 创建AbstractMapper的具体子类用于测试
     */
    private function createConcreteMapper(
        int      $id = 0,
        string   $prgData = '',
        string   $chrData = '',
        ?SaveRam $sram = null,
        int      $mirroringMode = MirroringMode::HORIZONTAL
    ): AbstractMapper
    {
        return new class($id, new PrgRom($prgData), new ChrRom($chrData), $sram, $mirroringMode) extends AbstractMapper {
            public function getId(): int
            {
                return $this->mapperId;
            }

            // 为测试暴露受保护的方法
            public function setIrqState(bool $state): void
            {
                parent::setIrqState($state);
            }
        };
    }

    /**
     * 测试构造函数和基本属性
     */
    public function testConstructor(): void
    {
        $mapperId = 42;
        $prgRom = new PrgRom(str_repeat("\xAA", 32 * 1024));
        $chrRom = new ChrRom(str_repeat("\xBB", 8 * 1024));
        $saveRam = new SaveRam(8 * 1024, true);
        $mirroringMode = MirroringMode::VERTICAL;

        $mapper = $this->createConcreteMapper($mapperId, $prgRom->getData(), $chrRom->getData(), $saveRam, $mirroringMode);

        $this->assertSame($mapperId, $mapper->getId());
        $this->assertSame($mirroringMode, $mapper->getMirroringMode());
        $this->assertFalse($mapper->irqState());
    }

    /**
     * 测试设置镜像模式
     */
    public function testSetMirroringMode(): void
    {
        $mapper = $this->createConcreteMapper();

        // 默认为水平镜像
        $this->assertSame(MirroringMode::HORIZONTAL, $mapper->getMirroringMode());

        // 切换到垂直镜像
        $mapper->setMirroringMode(MirroringMode::VERTICAL);
        $this->assertSame(MirroringMode::VERTICAL, $mapper->getMirroringMode());

        // 无效的镜像模式应该被忽略
        $mapper->setMirroringMode(99);
        $this->assertSame(MirroringMode::VERTICAL, $mapper->getMirroringMode());
    }

    /**
     * 测试IRQ状态管理
     */
    public function testIrqState(): void
    {
        $mapper = $this->createConcreteMapper();

        // 默认IRQ状态为false
        $this->assertFalse($mapper->irqState());

        // 设置IRQ状态
        $mapper->setIrqState(true);
        $this->assertTrue($mapper->irqState());

        // 清除IRQ状态
        $mapper->clearIrq();
        $this->assertFalse($mapper->irqState());
    }

    /**
     * 测试基本的CPU读写操作（应该在子类中实现）
     */
    public function testCpuReadWrite(): void
    {
        $mapper = $this->createConcreteMapper();

        // CPU读取应该返回0（抽象类的默认行为）
        $this->assertSame(0, $mapper->cpuRead(0x8000));

        // CPU写入不应有问题
        $mapper->cpuWrite(0x8000, 0xAA);
        $this->assertTrue(true); // 没有异常即为通过
    }

    /**
     * 测试基本的PPU读写操作（应该在子类中实现）
     */
    public function testPpuReadWrite(): void
    {
        $mapper = $this->createConcreteMapper();

        // PPU读取应该返回0（抽象类的默认行为）
        $this->assertSame(0, $mapper->ppuRead(0x0000));

        // PPU写入不应有问题
        $mapper->ppuWrite(0x0000, 0xBB);
        $this->assertTrue(true); // 没有异常即为通过
    }
}
