<?php

namespace Tourze\NES\MMC\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tourze\NES\MMC\MirroringMode;

/**
 * 测试名称表镜像模式枚举
 */
class MirroringModeTest extends TestCase
{
    /**
     * 测试枚举是否具有所有必要的镜像模式常量
     */
    public function testHasRequiredConstants(): void
    {
        $this->assertSame(0, MirroringMode::HORIZONTAL);
        $this->assertSame(1, MirroringMode::VERTICAL);
        $this->assertSame(2, MirroringMode::FOUR_SCREEN);
        $this->assertSame(3, MirroringMode::ONE_SCREEN_LOWER);
        $this->assertSame(4, MirroringMode::ONE_SCREEN_UPPER);
    }

    /**
     * 测试镜像模式是否有效的验证方法
     */
    public function testIsValidMirroringMode(): void
    {
        $this->assertTrue(MirroringMode::isValid(MirroringMode::HORIZONTAL));
        $this->assertTrue(MirroringMode::isValid(MirroringMode::VERTICAL));
        $this->assertTrue(MirroringMode::isValid(MirroringMode::FOUR_SCREEN));
        $this->assertTrue(MirroringMode::isValid(MirroringMode::ONE_SCREEN_LOWER));
        $this->assertTrue(MirroringMode::isValid(MirroringMode::ONE_SCREEN_UPPER));

        // 无效的值
        $this->assertFalse(MirroringMode::isValid(-1));
        $this->assertFalse(MirroringMode::isValid(5));
    }
    
    /**
     * 测试获取镜像模式名称的方法
     */
    public function testGetModeName(): void
    {
        $this->assertSame('HORIZONTAL', MirroringMode::getModeName(MirroringMode::HORIZONTAL));
        $this->assertSame('VERTICAL', MirroringMode::getModeName(MirroringMode::VERTICAL));
        $this->assertSame('FOUR_SCREEN', MirroringMode::getModeName(MirroringMode::FOUR_SCREEN));
        $this->assertSame('ONE_SCREEN_LOWER', MirroringMode::getModeName(MirroringMode::ONE_SCREEN_LOWER));
        $this->assertSame('ONE_SCREEN_UPPER', MirroringMode::getModeName(MirroringMode::ONE_SCREEN_UPPER));
        
        // 无效的值
        $this->assertSame('UNKNOWN', MirroringMode::getModeName(99));
    }
}
