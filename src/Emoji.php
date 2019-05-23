<?php

namespace Archman\Unicode;

class Emoji
{
    const VERSION_12_0 = '12.0';

    private static $versionsConfig = [
        self::VERSION_12_0 => __DIR__.'/config/emoji_v120.php',
    ];

    private static $versions = [];

    public function __construct(string $version)
    {
        if (!isset(self::$versions[$version])) {
            self::$versions[$version] = require self::$versionsConfig[$version];
        }
    }
}
