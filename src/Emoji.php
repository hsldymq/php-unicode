<?php

namespace Archman\Unicode;

class Emoji
{
    const VERSION_12_0 = '12.0';

    private static $versionsConfig = [
        self::VERSION_12_0 => __DIR__.'/config/emoji_v120.php',
    ];

    private static $versions = [];

    private $currentVersion;

    /**
     * @param string $version 初始化不传递版本号则将会在今后包的升级中自动升级为新的版本
     */
    public function __construct(string $version = self::VERSION_12_0)
    {
        if (!isset(self::$versions[$version])) {
            self::$versions[$version] = require self::$versionsConfig[$version];
        }

        $this->currentVersion = self::$versions[$version];
    }

    /**
     * 指定的码点数组中是否包含emoji.
     *
     * @param int[] $codePointArr
     *
     * @return bool
     */
    public function containEmoji(array $codePointArr): bool
    {
        $checkedPrefix = [];
        foreach ($codePointArr as $index => $each) {
            $prefix = sprintf("%04X", $each);
            if (!isset($this->currentVersion[$prefix]) || isset($checkedPrefix[$prefix])) {
                continue;
            }

            $sequences = $this->SequencesWithPrefix($prefix);
            $checkedPrefix[$prefix] = true;
            foreach ($sequences as $seq) {
                if ($codePointArr == $seq) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 返回以指定code point开头的所有emoji code point序列.
     *
     * @param string $prefix
     *
     * @return int[][]
     */
    private function SequencesWithPrefix(string $prefix): array
    {
        $result = [];
        $this->collectSequences($this->currentVersion[$prefix], [hexdec($prefix)], $result);

        return $result;
    }

    private function collectSequences(array $node, array $prefix, &$result)
    {
        if ($node[''] ?? false) {
            $result[]= $prefix;
            unset($node['']);
        }

        foreach ($node as $codePoint => $each) {
            $this->collectSequences($node[$codePoint], array_merge($prefix, [hexdec($codePoint)]), $result);
        }
    }
}
