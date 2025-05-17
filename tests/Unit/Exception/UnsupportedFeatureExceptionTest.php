<?php

namespace Tourze\NES\MMC\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use Tourze\NES\MMC\Exception\MapperException;
use Tourze\NES\MMC\Exception\UnsupportedFeatureException;

/**
 * 测试不支持的功能异常
 */
class UnsupportedFeatureExceptionTest extends TestCase
{
    /**
     * 测试异常继承自MapperException
     */
    public function testExtendsMapperException(): void
    {
        $exception = new UnsupportedFeatureException('测试不支持的功能');
        $this->assertInstanceOf(MapperException::class, $exception);
    }

    /**
     * 测试可以设置功能名称
     */
    public function testCanSetFeatureName(): void
    {
        $featureName = 'IRQ';
        $exception = new UnsupportedFeatureException('映射器不支持IRQ功能', 0, null, $featureName);

        $this->assertSame($featureName, $exception->getFeatureName());
        $this->assertStringContainsString($featureName, $exception->getMessage());
    }
}
