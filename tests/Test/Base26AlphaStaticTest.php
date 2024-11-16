<?php

namespace Tests\Unit;

use Aoaite\BunnyHash\BunnyHash;
use Aoaite\BunnyHash\Encodings\Base26AlphaEncoding;
use ArithmeticError;
use PHPUnit\Framework\TestCase;

class Base26AlphaStaticTest extends TestCase
{
    private BunnyHash $bunny;

    protected function prepareBunny(string $prime, int $length, ?string $offset): void
    {
        $this->bunny = new BunnyHash($prime, new Base26AlphaEncoding($length), $offset);
    }

    public function test_bunny_hash_init(): void
    {
        $this->prepareBunny('57885161', 6, '0');

        $this->assertTrue($this->bunny->hash(26) == 'WRKYJA');

        $this->assertTrue($this->bunny->reverse('WRKYJA') == 26);
    }

    public function test_bunny_regression(): void
    {
        $this->prepareBunny('57885161', 3, '0');
        for ($i = 0; $i < $this->bunny->getCapacity(); $i++) {
            $hash = $this->bunny->hash($i);
            $this->assertEquals($this->bunny->reverse($hash), $i);
        }
    }

    public function test_auto_offset(): void
    {
        $this->prepareBunny('57885161', 4, null);
        $this->assertEquals($this->bunny->getOffset(), "228488");
    }

    public function test_too_big_exceptions(): void
    {
        $this->prepareBunny('961748941', 1, '1');
        $this->expectException(ArithmeticError::class);
        $this->bunny->hash(26);
    }

    public function test_negative_exceptions(): void
    {
        $this->prepareBunny('961748941', 10, '3');
        $this->expectException(ArithmeticError::class);
        $this->bunny->hash(-100);
    }

    public function test_big_offset_exceptions(): void
    {
        $this->expectException(ArithmeticError::class);
        $this->prepareBunny('961748941', 2, '373953475843534');
    }

    public function test_negative_offset_exceptions(): void
    {
        $this->expectException(ArithmeticError::class);
        $this->prepareBunny('961748941', 2, '-1');
    }
}
