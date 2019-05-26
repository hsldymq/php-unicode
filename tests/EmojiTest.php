<?php

use PHPUnit\Framework\TestCase;
use \Archman\Unicode\Emoji;

class EmojiTest extends TestCase
{
    public function testContainEmoji()
    {
        $emoji = new Emoji();
        $this->assertTrue($emoji->containEmoji([0x1F64C]));
        $this->assertTrue($emoji->containEmoji([0x1F64C, 0x1F3FC]));
        $this->assertTrue($emoji->containEmoji([0x1F468, 0x1F3FE]));
        $this->assertTrue($emoji->containEmoji([0x1F471, 0x200D, 0x2642]));
        $this->assertTrue($emoji->containEmoji([0x1F471, 0x200D, 0x2642, 0xFE0F]));
        $this->assertFalse($emoji->containEmoji([0x1F471, 0x200D]));
        $this->assertFalse($emoji->containEmoji([0x1]));
        $this->assertFalse($emoji->containEmoji([]));
    }
}