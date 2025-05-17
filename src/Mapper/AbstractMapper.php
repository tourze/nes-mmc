<?php

namespace Tourze\NES\MMC\Mapper;

use Tourze\NES\Cartridge\Memory\ChrRom;
use Tourze\NES\Cartridge\Memory\PrgRom;
use Tourze\NES\Cartridge\Memory\SaveRam;
use Tourze\NES\MMC\MirroringMode;

/**
 * 映射器抽象基类
 *
 * 为所有具体映射器实现提供公共功能
 */
abstract class AbstractMapper implements MapperInterface
{
    /**
     * 映射器ID
     */
    protected int $mapperId;

    /**
     * 程序ROM
     */
    protected PrgRom $prgRom;

    /**
     * 图形ROM
     */
    protected ChrRom $chrRom;

    /**
     * 存档RAM
     */
    protected ?SaveRam $saveRam;

    /**
     * 镜像模式
     */
    protected int $mirroringMode;

    /**
     * IRQ中断状态
     */
    protected bool $irqActive = false;

    /**
     * 创建映射器实例
     *
     * @param int $mapperId 映射器ID
     * @param PrgRom $prgRom 程序ROM
     * @param ChrRom $chrRom 图形ROM
     * @param SaveRam|null $saveRam 存档RAM
     * @param int $mirroringMode 镜像模式
     */
    public function __construct(
        int $mapperId,
        PrgRom $prgRom,
        ChrRom $chrRom,
        ?SaveRam $saveRam = null,
        int $mirroringMode = MirroringMode::HORIZONTAL
    )
    {
        $this->mapperId = $mapperId;
        $this->prgRom = $prgRom;
        $this->chrRom = $chrRom;
        $this->saveRam = $saveRam;

        // 确保镜像模式有效
        if (MirroringMode::isValid($mirroringMode)) {
            $this->mirroringMode = $mirroringMode;
        } else {
            $this->mirroringMode = MirroringMode::HORIZONTAL;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMirroringMode(): int
    {
        return $this->mirroringMode;
    }

    /**
     * {@inheritdoc}
     */
    public function setMirroringMode(int $mode): void
    {
        if (MirroringMode::isValid($mode)) {
            $this->mirroringMode = $mode;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function irqState(): bool
    {
        return $this->irqActive;
    }

    /**
     * {@inheritdoc}
     */
    public function clearIrq(): void
    {
        $this->irqActive = false;
    }

    /**
     * 设置IRQ中断状态
     *
     * @param bool $state 新的IRQ状态
     */
    protected function setIrqState(bool $state): void
    {
        $this->irqActive = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function cpuRead(int $address): int
    {
        // 默认实现返回0，子类应该重写此方法
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function cpuWrite(int $address, int $data): void
    {
        // 默认实现不执行任何操作，子类应该重写此方法
    }

    /**
     * {@inheritdoc}
     */
    public function ppuRead(int $address): int
    {
        // 默认实现返回0，子类应该重写此方法
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function ppuWrite(int $address, int $data): void
    {
        // 默认实现不执行任何操作，子类应该重写此方法
    }

    /**
     * {@inheritdoc}
     */
    public function reset(): void
    {
        // 重置IRQ状态
        $this->irqActive = false;
    }

    /**
     * {@inheritdoc}
     */
    public function clockCpu(): void
    {
        // 默认实现不执行任何操作，子类应该根据需要重写此方法
    }

    /**
     * 获取程序ROM
     *
     * @return PrgRom
     */
    protected function getPrgRom(): PrgRom
    {
        return $this->prgRom;
    }

    /**
     * 获取图形ROM
     *
     * @return ChrRom
     */
    protected function getChrRom(): ChrRom
    {
        return $this->chrRom;
    }

    /**
     * 获取存档RAM
     *
     * @return SaveRam|null
     */
    protected function getSaveRam(): ?SaveRam
    {
        return $this->saveRam;
    }
}
