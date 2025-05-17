<?php

namespace Tourze\NES\MMC;

use Tourze\NES\Cartridge\Memory\ChrRom;
use Tourze\NES\Cartridge\Memory\PrgRom;
use Tourze\NES\Cartridge\Memory\SaveRam;
use Tourze\NES\MMC\Exception\UnsupportedFeatureException;
use Tourze\NES\MMC\Mapper\MapperInterface;
use Tourze\NES\MMC\Registry\MapperRegistry;

/**
 * 映射器工厂
 *
 * 用于创建不同类型的映射器实例
 */
class MapperFactory
{
    /**
     * 创建映射器实例
     *
     * @param int $mapperType 映射器类型ID
     * @param PrgRom $prgRom 程序ROM
     * @param ChrRom $chrRom 图形ROM
     * @param SaveRam|null $saveRam 存档RAM
     * @param int $mirroringMode 镜像模式
     * @return MapperInterface 创建的映射器实例
     * @throws UnsupportedFeatureException 如果映射器类型不支持
     */
    public static function create(
        int $mapperType,
        PrgRom $prgRom,
        ChrRom $chrRom,
        ?SaveRam $saveRam = null,
        int $mirroringMode = MirroringMode::HORIZONTAL
    ): MapperInterface {
        // 获取映射器类名
        $mapperClass = MapperRegistry::getMapperClass($mapperType);

        // 创建映射器实例
        return new $mapperClass(
            $prgRom,
            $chrRom,
            $saveRam,
            $mirroringMode
        );
    }

    /**
     * 确保默认映射器已注册
     */
    public static function ensureDefaultMappersRegistered(): void
    {
        // 如果没有任何映射器被注册，则注册默认映射器
        if (!MapperRegistry::isMapperSupported(0)) {
            MapperRegistry::registerDefaultMappers();
        }
    }
}
