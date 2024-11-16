<?php

namespace Tests\Unit;

use Aoaite\BunnyHash\BunnyHash;
use Aoaite\BunnyHash\Encodings\Base26AlphaEncoding;
use ArithmeticError;
use PHPUnit\Framework\TestCase;

class Base26AlphaDynamicTest extends TestCase
{
    private BunnyHash $bunny;

    protected function prepareBunny(string $prime, int $length, string $offset): void
    {
        $this->bunny = new BunnyHash($prime, new Base26AlphaEncoding($length), $offset);
    }

    public function test_bunny_regression(): void
    {
        $this->prepareBunny('293669395806410553378434342688492926719', 10, '9247213452735');
        for ($i = 0; $i < 10000; $i++) {
            $hash = $this->bunny->hash($i);
            $this->assertEquals($this->bunny->reverse($hash), $i);
        }
    }

    public function test_too_big_exceptions(): void
    {
        $this->prepareBunny('961748941', 1, '982451653');
        $this->expectException(ArithmeticError::class);
        $this->bunny->hash(26);
    }

    public function test_negative_exceptions(): void
    {
        $this->prepareBunny('961748941', 10, '982451653');
        $this->expectException(ArithmeticError::class);
        $this->bunny->hash(-100);
    }
}
