<?php

namespace Tourze\NES\MMC\Mapper;

/**
 * 映射器接口
 *
 * 定义所有NES映射器必须实现的方法
 * 不同的映射器实现会有不同的内存映射逻辑和特殊功能
 */
interface MapperInterface
{
    /**
     * 获取映射器ID
     *
     * @return int 映射器ID
     */
    public function getId(): int;

    /**
     * CPU读取操作
     * 将CPU地址空间映射到适当的内存组件
     *
     * @param int $address CPU地址空间中的地址
     * @return int 读取的数据
     */
    public function cpuRead(int $address): int;

    /**
     * CPU写入操作
     * 将CPU地址空间映射到适当的内存组件
     *
     * @param int $address CPU地址空间中的地址
     * @param int $data 要写入的数据
     */
    public function cpuWrite(int $address, int $data): void;

    /**
     * PPU读取操作
     * 将PPU地址空间映射到适当的内存组件
     *
     * @param int $address PPU地址空间中的地址
     * @return int 读取的数据
     */
    public function ppuRead(int $address): int;

    /**
     * PPU写入操作
     * 将PPU地址空间映射到适当的内存组件
     *
     * @param int $address PPU地址空间中的地址
     * @param int $data 要写入的数据
     */
    public function ppuWrite(int $address, int $data): void;

    /**
     * 重置映射器状态
     * 在系统重置或加载新游戏时调用
     */
    public function reset(): void;

    /**
     * 获取当前镜像模式
     *
     * @return int 当前镜像模式（参见MirroringMode类）
     */
    public function getMirroringMode(): int;

    /**
     * 设置镜像模式
     *
     * @param int $mode 要设置的镜像模式
     */
    public function setMirroringMode(int $mode): void;

    /**
     * 获取IRQ中断状态
     *
     * @return bool 如果IRQ中断激活则返回true
     */
    public function irqState(): bool;

    /**
     * 清除IRQ中断状态
     */
    public function clearIrq(): void;

    /**
     * 时钟信号 - 模拟CPU周期
     * 用于处理映射器内部计时器
     */
    public function clockCpu(): void;
}
