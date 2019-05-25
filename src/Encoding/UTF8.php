<?php

namespace Archman\Unicode\Encoding;

class UTF8
{
    private static $mask = [
        1 => 0x7F,
        2 => 0x1F,
        3 => 0x0F,
        4 => 0x07,
    ];

    /**
     * 将UTF-8编码的字符串转换为unicode code point数组.
     *
     * @param string $str
     *
     * @return int[]
     */
    public static function getCodePointArray(string $str): array
    {
        $ret = [];
        foreach (self::iterChar($str) as $char) {
            $ret[] = self::getCodePoint($char);
        }

        return $ret;
    }

    /**
     * 获得字符的code point.
     *
     * @param string $char
     *
     * @return int
     * @throws \InvalidArgumentException
     */
    public static function getCodePoint(string $char): int
    {
        if (strlen($char) === 0) {
            throw new \InvalidArgumentException('Not allow empty string');
        }

        $charLen = self::charLen($char[0]);
        $codePoint = 0;
        for ($i = 0; $i < $charLen; $i++) {
            $byte = ord($char[$i]);

            if ($i === 0) {
                $codePoint = $byte & self::$mask[$charLen];
            } else {
                $codePoint = ($codePoint << 6) | ($byte & 0x3F);
            }
        }

        return $codePoint;
    }

    private static function iterChar(string $str)
    {
        if (function_exists('mb_strlen')) {
            for ($i = 0, $strLen = mb_strlen($str, 'UTF-8'); $i < $strLen; $i++) {
                yield mb_substr($str, $i, 1, 'UTF-8');
            }
        } else {
            $pos = 0;
            while ($pos < strlen($str)) {
                $len = self::charLen($str[$pos]);
                yield substr($str, $pos, $len);
                $pos += $len;
            }
        }
    }

    private static function charLen(string $char): int
    {
        $byte = ord($char);

        switch (true) {
            case ($byte & 0b11110000) === 0b11110000:
                $len = 4;
                break;
            case ($byte & 0b11100000) === 0b11100000:
                $len = 3;
                break;
            case ($byte & 0b11000000) === 0b11000000:
                $len = 2;
                break;
            default:
                $len = 1;
        }

        return $len;
    }
}