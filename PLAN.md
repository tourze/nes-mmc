# NES映射器模块开发计划

## 1. 概述

NES映射器(Memory Management Controller, MMC)模块负责实现不同类型的NES内存映射芯片，主要功能包括：
- 管理CPU和PPU地址空间的内存映射
- 处理库切换(Bank Switching)逻辑
- 生成硬件中断(IRQ)信号
- 控制名称表镜像方式

本模块将与卡带模块(`nes-cartridge`)交互，提供100多种不同映射器类型的模拟实现。

## 2. 目录结构

```shell
nes-mmc/
├── src/
│   ├── Mapper/                    # 映射器实现
│   │   ├── MapperInterface.php    # 映射器通用接口 (已完成)
│   │   ├── AbstractMapper.php     # 映射器抽象基类 (已完成)
│   │   ├── Mapper000.php          # 映射器#0 (NROM) (已完成)
│   │   ├── Mapper001.php          # 映射器#1 (MMC1)
│   │   ├── Mapper002.php          # 映射器#2 (UxROM)
│   │   ├── Mapper003.php          # 映射器#3 (CNROM)
│   │   ├── Mapper004.php          # 映射器#4 (MMC3)
│   │   └── ...                    # 其他映射器
│   ├── Exception/                 # 异常处理
│   │   ├── MapperException.php    # 映射器异常基类 (已完成)
│   │   └── UnsupportedFeatureException.php # 不支持的功能异常 (已完成)
│   ├── Registry/                  # 映射器注册表
│   │   └── MapperRegistry.php     # 映射器类型注册表 (已完成)
│   ├── MirroringMode.php          # 名称表镜像模式枚举 (已完成)
│   └── MapperFactory.php          # 映射器工厂类 (已完成)
├── tests/                         # 测试目录
│   ├── Unit/                      # 单元测试
│   │   ├── Mapper/                # 映射器测试 (部分完成)
│   │   └── Registry/              # 注册表测试 (已完成)
│   └── Integration/               # 集成测试
└── README.md                      # 文档 (已完成)
```

## 3. 模块分层设计

1. **接口层**
   - `MapperInterface` - 定义所有映射器必须实现的接口 (已完成)
   - `MirroringMode` - 名称表镜像模式的枚举类型 (已完成)

2. **抽象层**
   - `AbstractMapper` - 提供基本映射器实现和共享功能 (已完成)

3. **实现层**
   - 针对不同映射器类型的具体实现类(Mapper000, Mapper001等) (部分完成)

4. **工厂层**
   - `MapperFactory` - 根据映射器ID创建对应的映射器实例 (已完成)

5. **注册层**
   - `MapperRegistry` - 管理映射器类型与实现类的关联 (已完成)

## 4. 类级别设计

### 接口与抽象类

- **MapperInterface**：定义映射器的公共接口 (已完成)
  - 方法：`cpuRead`, `cpuWrite`, `ppuRead`, `ppuWrite`, `reset`
  - 方法：`getId`, `getMirroringMode`, `setMirroringMode`
  - 方法：`irqState` - 获取IRQ中断状态

- **AbstractMapper**：映射器抽象基类 (已完成)
  - 属性：映射器ID、`PrgRom`引用、`ChrRom`引用、`SaveRam`引用
  - 功能：实现基本的映射器功能，提供子类可复用的方法

- **MirroringMode**：镜像模式枚举 (已完成)
  - 常量：`HORIZONTAL`, `VERTICAL`, `FOUR_SCREEN`, `ONE_SCREEN_LOWER`, `ONE_SCREEN_UPPER`

### 基本映射器实现

- **Mapper000 (NROM)**：最基本的无库切换映射器 (已完成)
  - 支持16KB或32KB PRG ROM
  - 支持8KB CHR ROM或RAM

- **Mapper001 (MMC1)**：任天堂的MMC1芯片
  - 支持分离PRG ROM库
  - 支持分离CHR ROM库
  - 支持RAM和电池
  - 支持控制镜像模式

- **Mapper002 (UxROM)**：PRG库切换映射器
  - 支持切换16KB PRG ROM库

- **Mapper003 (CNROM)**：CHR库切换映射器
  - 支持切换8KB CHR ROM库

- **Mapper004 (MMC3)**：任天堂的MMC3芯片
  - 支持更复杂的库切换
  - 支持IRQ生成
  - 支持RAM和电池

### 工厂与注册

- **MapperFactory**：创建映射器实例 (已完成)
  - 方法：`create` - 根据映射器ID和ROM数据创建映射器实例

- **MapperRegistry**：管理映射器类型 (已完成)
  - 方法：`register`, `getMapperClass` - 注册和获取映射器实现类

### 异常处理

- **MapperException**：映射器异常基类 (已完成)
  - 处理一般映射器错误

- **UnsupportedFeatureException**：不支持的功能异常 (已完成)
  - 处理映射器不支持的特殊功能请求

## 5. 完成进度规划

| 模块 | 类 | 状态 | 优先级 | 依赖项 |
|------|-----|------|--------|--------|
| 接口 | MapperInterface | 已完成 | 最高 | 无 |
| 接口 | MirroringMode | 已完成 | 高 | 无 |
| 映射器 | AbstractMapper | 已完成 | 最高 | MapperInterface |
| 映射器 | Mapper000 | 已完成 | 高 | AbstractMapper |
| 映射器 | Mapper001 | 未开始 | 中 | AbstractMapper |
| 映射器 | Mapper002 | 未开始 | 中 | AbstractMapper |
| 映射器 | Mapper003 | 未开始 | 中 | AbstractMapper |
| 映射器 | Mapper004 | 未开始 | 中 | AbstractMapper |
| 异常 | MapperException | 已完成 | 高 | 无 |
| 异常 | UnsupportedFeatureException | 已完成 | 中 | MapperException |
| 注册 | MapperRegistry | 已完成 | 高 | 无 |
| 工厂 | MapperFactory | 已完成 | 高 | MapperRegistry, AbstractMapper |

## 6. 实施步骤

1. **阶段1：基础接口与结构** (已完成)
   - 实现MirroringMode枚举
   - 实现MapperInterface接口
   - 实现异常类
   
2. **阶段2：抽象层** (已完成)
   - 实现AbstractMapper抽象类
   - 实现基础的内存映射逻辑

3. **阶段3：简单映射器** (部分完成)
   - 实现Mapper000 (NROM) (已完成)
   - 实现Mapper003 (CNROM)
   - 实现Mapper002 (UxROM)

4. **阶段4：复杂映射器**
   - 实现Mapper001 (MMC1)
   - 实现Mapper004 (MMC3)

5. **阶段5：注册与工厂** (已完成)
   - 实现MapperRegistry
   - 实现MapperFactory

6. **阶段6：扩展映射器**
   - 实现其他常用映射器

7. **阶段7：完善和测试** (进行中)
   - 实现完整的测试覆盖
   - 性能优化
   - 文档完善

## 7. 与卡带模块的交互

映射器模块(`nes-mmc`)与卡带模块(`nes-cartridge`)的交互设计：

1. **依赖关系**：
   - 映射器模块依赖卡带模块提供的ROM和RAM数据
   - 映射器模块通过卡带模块的接口获取数据

2. **数据流向**：
   - 卡带模块负责加载ROM文件和解析头信息
   - 映射器模块负责处理CPU/PPU地址映射
   - CPU/PPU总线访问先到达卡带模块，再由卡带模块转发给映射器模块

3. **初始化流程**：
   - 卡带模块创建卡带实例
   - 卡带模块确定映射器类型
   - 映射器工厂根据类型创建映射器实例
   - 映射器实例接收卡带的ROM/RAM引用

## 8. 优先级说明

映射器功能的实现优先级应基于游戏的使用频率：

1. 优先级最高：
   - Mapper000 (NROM) - 最基本的映射器，用于早期游戏如《超级马里奥》 (已完成)

2. 优先级高：
   - Mapper001 (MMC1) - 用于《塞尔达传说》等
   - Mapper004 (MMC3) - 用于《超级马里奥兄弟3》等

3. 优先级中：
   - Mapper002 (UxROM) - 多款游戏使用
   - Mapper003 (CNROM) - 多款游戏使用

4. 优先级低：
   - 其他不太常见的映射器
