<?php

namespace Tourze\NES\MMC;

/**
 * 名称表镜像模式枚举
 *
 * NES PPU名称表的镜像方式，决定了游戏图形的渲染方式
 */
class MirroringMode
{
    /**
     * 水平镜像模式
     * 名称表0镜像到名称表1，名称表2镜像到名称表3
     */
    public const HORIZONTAL = 0;

    /**
     * 垂直镜像模式
     * 名称表0镜像到名称表2，名称表1镜像到名称表3
     */
    public const VERTICAL = 1;

    /**
     * 四屏模式
     * 所有名称表都是独立的，不进行镜像
     */
    public const FOUR_SCREEN = 2;

    /**
     * 单屏模式（使用下方屏幕）
     * 名称表0被复制到所有其他名称表
     */
    public const ONE_SCREEN_LOWER = 3;

    /**
     * 单屏模式（使用上方屏幕）
     * 名称表1被复制到所有其他名称表
     */
    public const ONE_SCREEN_UPPER = 4;

    /**
     * 检查给定的镜像模式是否有效
     *
     * @param int $mode 要检查的镜像模式
     * @return bool 如果镜像模式有效则返回true
     */
    public static function isValid(int $mode): bool
    {
        return $mode >= self::HORIZONTAL && $mode <= self::ONE_SCREEN_UPPER;
    }

    /**
     * 获取镜像模式的名称
     *
     * @param int $mode 镜像模式
     * @return string 镜像模式的名称
     */
    public static function getModeName(int $mode): string
    {
        return match ($mode) {
            self::HORIZONTAL => 'HORIZONTAL',
            self::VERTICAL => 'VERTICAL',
            self::FOUR_SCREEN => 'FOUR_SCREEN',
            self::ONE_SCREEN_LOWER => 'ONE_SCREEN_LOWER',
            self::ONE_SCREEN_UPPER => 'ONE_SCREEN_UPPER',
            default => 'UNKNOWN',
        };
    }
}
