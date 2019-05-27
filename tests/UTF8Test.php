<?php

use PHPUnit\Framework\TestCase;
use Archman\Unicode\Encoding\UTF8;

class UTF8Test extends TestCase
{
    public function testGetCodePointArray()
    {
        $this->assertEquals([0x68, 0x65, 0x6C, 0x6C, 0x6F, 0x20, 0x77, 0x6F, 0x72, 0x6C, 0x64], UTF8::getCodePoints('hello world'));
        $this->assertEquals([0x4F60, 0x597D, 0xFF0C, 0x4E16, 0x754C], UTF8::getCodePoints("你好，世界"));
        $this->assertEquals([0x1F575, 0xFE0F], UTF8::getCodePoints("🕵️"));
        $this->assertEquals([0x1F577, 0xFE0F, 0x61, 0x1F3F5, 0xFE0F], UTF8::getCodePoints("🕷️a🏵️"));
    }

    public function testFromCodePoints()
    {
        $this->assertEquals('hello world', UTF8::fromCodePoints([0x68, 0x65, 0x6C, 0x6C, 0x6F, 0x20, 0x77, 0x6F, 0x72, 0x6C, 0x64]));
        $this->assertEquals('你好，世界', UTF8::fromCodePoints([0x4F60, 0x597D, 0xFF0C, 0x4E16, 0x754C]));
        $this->assertEquals('🕵️', UTF8::fromCodePoints([0x1F575, 0xFE0F]));
        $this->assertEquals('🕷️a🏵️', UTF8::fromCodePoints([0x1F577, 0xFE0F, 0x61, 0x1F3F5, 0xFE0F]));
    }
}