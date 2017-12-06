<?php
namespace Archman\Unicode;

class Unicode
{
    /**
     * 从UTF-8编码的字符串得到Unicode码点
     * @param string $string
     * @param bool $returnHex
     * @return array
     */
    public static function fromUTF8(string $string, bool $returnHex = true): array
    {
        $ret = [];
        for ($i = 0, $strLen = mb_strlen($string, 'UTF-8'); $i < $strLen; $i++) {
            $char = mb_substr($string, $i, 1, 'UTF-8');
            $codePoint = self::getCodePointFromUTF8($char);

            $ret[] = $returnHex ? strtoupper(dechex($codePoint)) : $codePoint;
        }

        return $ret;
    }

    private static function getCodePointFromUTF8(string $char): int
    {
        $codePoint = 0;
        $bitCount = 0;
        for ($bLen = strlen($char), $j = $bLen - 1; $j >= 0; $j--) {
            $byte = ord(substr($char, $j, 1));

            for ($bit = 0; $bit < ($j === 0 ? (7 - $bLen) : 6); $bit++, $bitCount++) {
                $codePoint |= (($byte & 1) << $bitCount);
                $byte >>= 1;
            }
        }

        return $codePoint;
    }
}