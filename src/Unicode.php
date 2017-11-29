<?php
namespace Archman\Unicode;

class Unicode
{
    public static function transferFromUTF8(string $string, bool $returnHex = true): array
    {
        $ret = [];
        for ($i = 0, $strLen = mb_strlen($string, 'UTF-8'); $i < $strLen; $i++) {
            $codePoint = 0;
            $bitCount = 0;
            $char = mb_substr($string, $i, 1, 'UTF-8');
            for ($bLen = strlen($char), $j = $bLen - 1; $j >= 0; $j--) {
                $byte = ord(substr($char, $j, 1));

                for ($bit = 0; $bit < ($j === 0 ? (7 - $bLen) : 6); $bit++, $bitCount++) {
                    $codePoint |= (($byte & 1) << $bitCount);
                    $byte >>= 1;
                }
            }

            $ret[] = $returnHex ? strtoupper(dechex($codePoint)) : $codePoint;
        }

        return $ret;
    }
}