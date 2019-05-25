<?php

namespace Archman\Unicode;

class Emoji
{
    const VERSION_12_0 = '12.0';

    private static $versionsConfig = [
        self::VERSION_12_0 => __DIR__.'/config/emoji_v120.php',
    ];

    private static $versions = [];

    /**
     * @param string $version 初始化不传递版本号则将会在今后包的升级中自动升级为新的版本
     */
    public function __construct(string $version = self::VERSION_12_0)
    {
        if (!isset(self::$versions[$version])) {
            self::$versions[$version] = require self::$versionsConfig[$version];
        }
    }
}
