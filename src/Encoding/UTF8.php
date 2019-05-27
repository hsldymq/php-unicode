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
     * 根据Unicode code point列表返回UTF-8编码的字符串.
     *
     * @param int[] $codePoints
     *
     * @return string
     */
    public static function fromCodePoints(array $codePoints): string
    {
        $bytes = [];
        foreach ($codePoints as $each) {
            if ($each > 0x10FFFF || $each < 0) {
                throw new \InvalidArgumentException('Code point out of range');
            }

            $hob = false;
            if ($each >= 65536) {
                $bytes[] = chr(($each >> 18) & 0x7 | 0xF0);
                $hob = true;
            }
            if ($each >= 2048) {
                $bytes[] = chr(($each >> 12) & ($hob ? 0xBF : 0x0F) | ($hob ? 0x80 : 0xE0));
                $hob = true;
            }
            if ($each >= 128) {
                $bytes[] = chr(($each >> 6) & ($hob ? 0xBF : 0x1F) | ($hob ? 0x80 : 0xC0));
                $hob = true;
            }
            $bytes[] = chr($each & ($hob ? 0xBF : 0x7F) | ($hob ? 0x80 : 0));
        }

        return implode('', $bytes);
    }

    /**
     * 将UTF-8编码的字符串转换为unicode code point数组.
     *
     * @param string $str
     *
     * @return int[]
     */
    public static function getCodePoints(string $str): array
    {
        $ret = [];
        foreach (self::iterChar($str) as $char) {
            $ret[] = self::getCharCodePoint($char);
        }

        return $ret;
    }

    /**
     * 获得单个字符的code point.
     *
     * @param string $char
     *
     * @return int
     * @throws \InvalidArgumentException
     */
    public static function getCharCodePoint(string $char): int
    {
        if (strlen($char) === 0) {
            throw new \InvalidArgumentException('Can not get code point from empty string');
        }

        $charLen = self::charLen($char[0]);
        $codePoint = ord($char[0]) & self::$mask[$charLen];
        for ($i = 1; $i < $charLen; $i++) {
            $codePoint = ($codePoint << 6) | (ord($char[$i]) & 0x3F);
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