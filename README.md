# NES Memory Mapper Controller (MMC)

This package provides an implementation of NES Memory Mapper Controller (MMC) for NES emulators, handling different types of mapper chips.

[中文文档](README.zh-CN.md)

## Features

- Support for multiple mapper types
- Unified mapper interface
- CPU and PPU address space mapping
- Bank switching support
- Different name table mirroring modes support
- IRQ interrupt generation

## Installation

Install via Composer:

```bash
composer require tourze/nes-mmc
```

## Usage

### Basic Usage

```php
use Tourze\NES\Cartridge\Memory\PrgRom;
use Tourze\NES\Cartridge\Memory\ChrRom;
use Tourze\NES\MMC\MapperFactory;
use Tourze\NES\MMC\MirroringMode;

// Create ROM data
$prgRom = new PrgRom($prgRomData);
$chrRom = new ChrRom($chrRomData);

// Create mapper instance - using mapper #0 (NROM)
$mapper = MapperFactory::create(0, $prgRom, $chrRom);

// CPU read
$data = $mapper->cpuRead(0x8000);

// CPU write
$mapper->cpuWrite(0x6000, 0x42);

// PPU read
$patternData = $mapper->ppuRead(0x0000);

// Change mirroring mode
$mapper->setMirroringMode(MirroringMode::VERTICAL);
```

### Register Custom Mapper

```php
use Tourze\NES\MMC\Registry\MapperRegistry;
use MyApp\Mappers\CustomMapper;

// Register custom mapper
MapperRegistry::register(123, CustomMapper::class);

// Use custom mapper
$mapper = MapperFactory::create(123, $prgRom, $chrRom);
```

## Supported Mappers

Currently supported mappers:

- Mapper #0 (NROM) - The most basic mapper without bank switching, used for games like Super Mario Bros
- More mappers coming soon...

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
