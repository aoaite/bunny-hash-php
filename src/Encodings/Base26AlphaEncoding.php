<?php

namespace Aoaite\BunnyHash\Encodings;

class Base26AlphaEncoding implements Encoding
{
    public function __construct(protected int $length) {}

    private const base = 26;

    public function encode(int $input): string
    {
        $result = '';
        $remainder = 0;
        while ($input > 0) {
            $remainder = $input % self::base;
            $result .= chr(65 + $remainder);
            $input = (int) ($input / self::base);
        }

        $result = strrev($result);

        while (strlen($result) < $this->length) {
            $result = 'A' . $result;
        }

        return $result;
    }

    public function decode(string $input): int
    {
        $result = 0;
        for ($i = 0; $i < $this->length; $i++) {
            $result *= 26;
            $result += ord($input[$i]) - 65;
        }
        return $result;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getBase(): int
    {
        return self::base;
    }
}
