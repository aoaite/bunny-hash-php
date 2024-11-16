<?php

namespace Aoaite\BunnyHash\Encodings;

interface Encoding
{
    public function encode(int $input): string;

    public function decode(string $input): int;

    public function getLength(): int;

    public function getBase(): int;
}
