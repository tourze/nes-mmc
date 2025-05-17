# NES内存映射器控制器(MMC)

这个包提供了NES内存映射器控制器(Memory Mapper Controller, MMC)的实现，用于在NES模拟器中处理不同类型的映射器芯片。

[English](README.md)

## 功能特性

- 支持多种映射器类型(Mapper)
- 提供统一的映射器接口
- 处理CPU和PPU地址空间的映射
- 支持库切换(Bank Switching)
- 支持不同的名称表镜像方式
- 提供IRQ中断生成功能

## 安装

通过Composer安装：

```bash
composer require tourze/nes-mmc
```

## 使用方法

### 基本使用

```php
use Tourze\NES\Cartridge\Memory\PrgRom;
use Tourze\NES\Cartridge\Memory\ChrRom;
use Tourze\NES\MMC\MapperFactory;
use Tourze\NES\MMC\MirroringMode;

// 创建ROM数据
$prgRom = new PrgRom($prgRomData);
$chrRom = new ChrRom($chrRomData);

// 创建映射器实例 - 这里使用映射器#0 (NROM)
$mapper = MapperFactory::create(0, $prgRom, $chrRom);

// CPU读取
$data = $mapper->cpuRead(0x8000);

// CPU写入
$mapper->cpuWrite(0x6000, 0x42);

// PPU读取
$patternData = $mapper->ppuRead(0x0000);

// 修改镜像模式
$mapper->setMirroringMode(MirroringMode::VERTICAL);
```

### 注册自定义映射器

```php
use Tourze\NES\MMC\Registry\MapperRegistry;
use MyApp\Mappers\CustomMapper;

// 注册自定义映射器
MapperRegistry::register(123, CustomMapper::class);

// 使用自定义映射器
$mapper = MapperFactory::create(123, $prgRom, $chrRom);
```

## 支持的映射器

目前支持以下映射器：

- Mapper #0 (NROM) - 最基本的无库切换映射器，用于《超级马里奥兄弟》等
- 更多映射器正在开发中...

## 许可证

本项目采用MIT许可证。详情请参见[LICENSE](LICENSE)文件。 