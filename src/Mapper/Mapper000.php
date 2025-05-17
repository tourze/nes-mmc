<?php

namespace Tourze\NES\MMC\Mapper;

use Tourze\NES\Cartridge\Memory\ChrRom;
use Tourze\NES\Cartridge\Memory\PrgRom;
use Tourze\NES\Cartridge\Memory\SaveRam;
use Tourze\NES\MMC\MirroringMode;

/**
 * Mapper #0 (NROM)
 *
 * 最基本的NES映射器，无库切换功能
 * 支持16KB或32KB的PRG ROM
 */
class Mapper000 extends AbstractMapper
{
    /**
     * 创建NROM映射器
     *
     * @param PrgRom $prgRom 程序ROM (16KB或32KB)
     * @param ChrRom $chrRom 图形ROM (8KB)
     * @param SaveRam|null $saveRam 存档RAM
     * @param int $mirroringMode 镜像模式
     */
    public function __construct(
        PrgRom $prgRom,
        ChrRom $chrRom,
        ?SaveRam $saveRam = null,
        int $mirroringMode = MirroringMode::HORIZONTAL
    ) {
        parent::__construct(0, $prgRom, $chrRom, $saveRam, $mirroringMode);
    }

    public function getId(): int
    {
        return 0; // NROM的映射器ID始终为0
    }

    public function cpuRead(int $address): int
    {
        // SRAM区域 (0x6000-0x7FFF)
        if ($address >= 0x6000 && $address <= 0x7FFF) {
            if ($this->saveRam !== null) {
                return $this->saveRam->read($address - 0x6000);
            }
            return 0;
        }

        // PRG ROM区域 (0x8000-0xFFFF)
        if ($address >= 0x8000 && $address <= 0xFFFF) {
            $prgSize = $this->prgRom->getSize();

            if ($prgSize <= 16 * 1024) {
                // 16KB PRG ROM镜像映射 (0x8000-0xBFFF 和 0xC000-0xFFFF 是相同的内容)
                return $this->prgRom->read(($address - 0x8000) % $prgSize);
            } else {
                // 32KB PRG ROM (0x8000-0xFFFF直接映射)
                return $this->prgRom->read($address - 0x8000);
            }
        }

        return 0; // 其他地址返回0
    }

    public function cpuWrite(int $address, int $data): void
    {
        // SRAM区域 (0x6000-0x7FFF)
        if ($address >= 0x6000 && $address <= 0x7FFF) {
            if ($this->saveRam !== null) {
                $this->saveRam->write($address - 0x6000, $data);
            }
        }

        // PRG ROM是只读的，忽略写入
    }

    public function ppuRead(int $address): int
    {
        // 图案表区域 (0x0000-0x1FFF)
        if ($address <= 0x1FFF) {
            return $this->chrRom->read($address);
        }

        return 0; // 其他PPU地址超出范围，返回0
    }

    public function ppuWrite(int $address, int $data): void
    {
        // 图案表区域 (0x0000-0x1FFF)
        if ($address <= 0x1FFF) {
            // 无论CHR ROM大小如何，都允许写入（若是实际ROM会被忽略）
            // 这样可支持CHR RAM测试（当size为0时）和内存对齐测试
            $this->chrRom->write($address, $data);
        }

        // 其他写入无效
    }

    public function reset(): void
    {
        parent::reset();
        // NROM没有额外的状态需要重置
    }

    public function clockCpu(): void
    {
        // NROM没有计时器，不需要实现此方法
    }
}
