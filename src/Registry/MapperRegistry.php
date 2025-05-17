<?php

namespace Tourze\NES\MMC\Registry;

use Tourze\NES\MMC\Exception\UnsupportedFeatureException;
use Tourze\NES\MMC\Mapper\Mapper000;

/**
 * 映射器注册表
 *
 * 管理映射器ID与实现类的映射关系
 */
class MapperRegistry
{
    /**
     * 已注册的映射器类
     *
     * @var array<int, string> 映射器ID => 映射器类名
     */
    private static array $mappers = [];

    /**
     * 注册一个映射器类
     *
     * @param int $mapperType 映射器ID
     * @param string $className 映射器实现类的类名
     */
    public static function register(int $mapperType, string $className): void
    {
        self::$mappers[$mapperType] = $className;
    }

    /**
     * 获取映射器类名
     *
     * @param int $mapperType 映射器ID
     * @return string 映射器实现类的类名
     * @throws UnsupportedFeatureException 如果映射器类型未注册
     */
    public static function getMapperClass(int $mapperType): string
    {
        if (!self::isMapperSupported($mapperType)) {
            throw new UnsupportedFeatureException("不支持的映射器类型：{$mapperType}", 0, null, "Mapper{$mapperType}");
        }

        return self::$mappers[$mapperType];
    }

    /**
     * 检查映射器类型是否支持
     *
     * @param int $mapperType 映射器ID
     * @return bool 如果该类型映射器已注册则返回true
     */
    public static function isMapperSupported(int $mapperType): bool
    {
        return isset(self::$mappers[$mapperType]);
    }

    /**
     * 重置注册表
     */
    public static function reset(): void
    {
        self::$mappers = [];
    }

    /**
     * 初始化默认映射器
     */
    public static function registerDefaultMappers(): void
    {
        self::register(0, Mapper000::class);
        // 其他映射器可以在此处注册
    }
}
