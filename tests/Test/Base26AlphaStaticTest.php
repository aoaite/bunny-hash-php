<?php

namespace Tests\Unit;

use Aoaite\BunnyHash\BunnyHash;
use Aoaite\BunnyHash\Encodings\Base26AlphaEncoding;
use ArithmeticError;
use PHPUnit\Framework\TestCase;

class Base26AlphaStaticTest extends TestCase
{
    private BunnyHash $bunny;

    protected function setUp(): void
    {
        $this->bunny = new BunnyHash("57885161", new Base26AlphaEncoding(6));
    }

    public function test_bunny_hash_init(): void
    {
        $this->assertTrue($this->bunny->hash(26) == 'WRKYJA');
        $this->assertTrue($this->bunny->reverse('WRKYJA') == 26);
    }

    public function test_bunny_regression(): void
    {
        for ($i = 0; $i < 10000; $i++) {
            $hash = $this->bunny->hash($i);
            $this->assertEquals($this->bunny->reverse($hash), $i);
        }
    }

    public function test_too_big_exceptions(): void
    {
        $this->expectException(ArithmeticError::class);
        $this->bunny->hash(101283412415);
    }

    public function test_negative_exceptions(): void
    {
        $this->expectException(ArithmeticError::class);
        $this->bunny->hash(-100);
    }
}
