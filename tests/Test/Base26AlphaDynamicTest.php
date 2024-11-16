<?php

namespace Tests\Unit;

use Aoaite\BaseEncoders\Encoders\Base26AlphaEncoder;
use Aoaite\BunnyHash\BunnyHash;
use PHPUnit\Framework\TestCase;

class Base26AlphaDynamicTest extends TestCase
{
    private BunnyHash $bunny;

    protected function prepareBunny(string $prime, int $length, ?string $offset): void
    {
        $this->bunny = new BunnyHash($prime, new Base26AlphaEncoder($length), $offset);
    }

    public function test_bunny_hash_init(): void
    {
        $this->prepareBunny('256290016518818249111859786246582687167', 6, null);

        $id = (string) rand(1, 9999999);

        $hash = $this->bunny->hash($id);

        $this->assertEquals($id, $this->bunny->reverse($hash));
    }

    public function test_bunny_regression(): void
    {
        $a = 62969;
        $length = 3;
        $this->prepareBunny($a, $length, (26 ^ $length) - 1);
        for ($i = 0; $i < $this->bunny->getCapacity(); $i++) {
            $hash = $this->bunny->hash($i);
            $this->assertEquals($this->bunny->reverse($hash), $i);
        }
    }
}
