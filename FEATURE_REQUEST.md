# NES-MMC模块功能增强请求

本文档详细说明了`nes-mmc`模块需要增强的功能，以便能够替代`nes-emulator`中的Mapper部分实现。

## 现状分析

`nes-mmc`模块目前提供了优秀的基础架构和设计，但与`nes-emulator`的Mapper实现相比，仍有一些功能差距需要弥补：

1. 实现的映射器类型数量有限（目前仅实现了Mapper000）
2. 缺少状态保存/恢复功能
3. 与卡带模块的交互方式不同
4. 缺少一些高级映射器特性的支持

## 需要增强的功能

### 1. 完善接口

需要对`MapperInterface`进行扩展，增加以下方法：

```php
/**
 * 获取映射器类型（枚举）
 * 
 * @return MapperType 映射器类型枚举
 */
public function getType(): MapperType;

/**
 * 获取映射器名称
 * 
 * @return string 映射器名称
 */
public function getName(): string;

/**
 * 获取当前状态（用于存档功能）
 * 
 * @return array 映射器状态数组
 */
public function getState(): array;

/**
 * 加载状态（用于读档功能）
 * 
 * @param array $state 映射器状态数组
 */
public function loadState(array $state): void;

/**
 * 扫描线计数器（某些映射器需要）
 */
public function scanlineCounter(): void;
```

### 2. 实现更多映射器类型

需要增加以下映射器实现：

- **Mapper001 (MMC1)** - 用于《塞尔达传说》等多款经典游戏
- **Mapper002 (UxROM)** - 用于《洛克人》系列等
- **Mapper003 (CNROM)** - 多款游戏使用
- **Mapper004 (MMC3)** - 用于《超级马里奥兄弟3》等
- **Mapper007 (AxROM)** - 用于《冒险岛》等
- **Mapper009/010 (MMC2/MMC4)** - 用于《美版马里奥兄弟2》等

### 3. 状态保存与恢复

在`AbstractMapper`类中实现基础的状态保存和恢复功能：

```php
/**
 * 获取当前状态
 * 
 * @return array 映射器状态数组
 */
public function getState(): array
{
    return [
        'mirroringMode' => $this->mirroringMode,
        'irqActive' => $this->irqActive,
        // 子类特定状态需要在子类中扩展
    ];
}

/**
 * 加载状态
 * 
 * @param array $state 映射器状态数组
 */
public function loadState(array $state): void
{
    $this->mirroringMode = $state['mirroringMode'] ?? MirroringMode::HORIZONTAL;
    $this->irqActive = $state['irqActive'] ?? false;
    // 子类特定状态需要在子类中处理
}
```

### 4. 增强与卡带的集成

创建一个适配器层或更新卡带模块，以便`nes-emulator`能够使用`nes-mmc`：

1. 创建卡带适配器，将`nes-emulator`的卡带接口转换为`nes-mmc`需要的ROM/RAM数据
2. 或者更新`MapperFactory`，添加从卡带创建映射器的方法：

```php
/**
 * 从卡带创建映射器
 * 
 * @param CartridgeInterface $cartridge 卡带实例
 * @return MapperInterface 创建的映射器实例
 */
public static function createFromCartridge(CartridgeInterface $cartridge): MapperInterface
{
    // 从卡带获取映射器类型
    $mapperType = $cartridge->getMapperType();
    
    // 从卡带获取ROM和RAM数据
    $prgRom = new PrgRom($cartridge->getPRGROMData());
    $chrRom = new ChrRom($cartridge->getCHRROMData());
    $saveRam = $cartridge->hasSRAM() ? new SaveRam($cartridge->getSRAMData()) : null;
    
    // 获取镜像模式
    $mirroringMode = self::convertMirrorType($cartridge->getMirrorType());
    
    // 创建映射器
    return self::create($mapperType, $prgRom, $chrRom, $saveRam, $mirroringMode);
}
```

### 5. 添加总线连接器

为了使映射器能够与模拟器各组件（如PPU和APU）进行通信，需要添加总线连接功能：

```php
/**
 * 连接到总线
 * 
 * @param BusInterface $bus 总线实例
 */
public function connectToBus(BusInterface $bus): void;
```

### 6. 增加类型枚举

添加`MapperType`枚举，用于标识不同类型的映射器：

```php
/**
 * 映射器类型枚举
 */
enum MapperType: int
{
    case NROM = 0;
    case MMC1 = 1;
    case UNROM = 2;
    case CNROM = 3;
    case MMC3 = 4;
    // 更多映射器类型...
    
    public function getDescription(): string
    {
        return match($this) {
            self::NROM => 'NROM',
            self::MMC1 => 'MMC1',
            self::UNROM => 'UxROM',
            self::CNROM => 'CNROM',
            self::MMC3 => 'MMC3',
            // 更多映射器描述...
            default => 'Unknown'
        };
    }
}
```

## 实施建议

1. **分阶段实现**：
   - 第一阶段：扩展接口和基类，实现状态保存/恢复功能
   - 第二阶段：实现更多映射器类型
   - 第三阶段：开发与`nes-emulator`的集成方案

2. **保持向后兼容**：
   - 确保对现有API的更改不会破坏现有功能
   - 新方法可以使用默认实现，以便现有代码不需要修改

3. **完善测试**：
   - 为每个映射器实现添加单元测试
   - 创建集成测试，确保与模拟器的兼容性

4. **文档更新**：
   - 更新README文档，说明新增功能和使用方法
   - 添加与`nes-emulator`集成的示例代码

## 优先级排序

1. **最高优先级**：
   - 扩展`MapperInterface`接口
   - 实现`getState`和`loadState`方法

2. **高优先级**：
   - 实现Mapper001 (MMC1)
   - 实现Mapper004 (MMC3)
   - 添加与卡带的集成方法

3. **中优先级**：
   - 实现Mapper002 (UxROM)
   - 实现Mapper003 (CNROM)
   - 添加`MapperType`枚举

4. **低优先级**：
   - 实现更多映射器类型
   - 完善文档和示例 