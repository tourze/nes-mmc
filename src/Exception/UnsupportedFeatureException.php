<?php

namespace Tourze\NES\MMC\Exception;

/**
 * 不支持的功能异常
 * 当映射器不支持某个特定功能时抛出
 */
class UnsupportedFeatureException extends MapperException
{
    /**
     * 不支持的功能名称
     */
    private ?string $featureName;
    
    /**
     * 创建不支持的功能异常
     *
     * @param string $message 异常信息
     * @param int $code 错误码
     * @param \Throwable|null $previous 上一个异常
     * @param string|null $featureName 不支持的功能名称
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
        ?string $featureName = null
    ) {
        $this->featureName = $featureName;
        
        // 如果有功能名称，添加到消息中
        if ($featureName !== null && $message === '') {
            $message = "不支持的功能：{$featureName}";
        } elseif ($featureName !== null) {
            // 如果消息中未包含功能名称，则添加
            if (strpos($message, $featureName) === false) {
                $message .= " (功能名称: {$featureName})";
            }
        }
        
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * 获取不支持的功能名称
     *
     * @return string|null 功能名称，如果未设置则为null
     */
    public function getFeatureName(): ?string
    {
        return $this->featureName;
    }
} 