<?php

namespace Tourze\NES\MMC\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use Tourze\NES\MMC\Exception\MapperException;

/**
 * 测试映射器异常基类
 */
class MapperExceptionTest extends TestCase
{
    /**
     * 测试异常的基本功能
     */
    public function testBasicFunctionality(): void
    {
        $message = '测试映射器异常';
        $code = 42;

        $exception = new MapperException($message, $code);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    /**
     * 测试异常可以被捕获
     */
    public function testCanBeCaught(): void
    {
        $caught = false;

        try {
            throw new MapperException('测试异常');
        } catch (MapperException $e) {
            $caught = true;
        }

        $this->assertTrue($caught, '映射器异常应该能被捕获');
    }
}
