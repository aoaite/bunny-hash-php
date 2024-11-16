<?php

namespace Aoaite\BunnyHash;

use Aoaite\BaseEncoders\FixedLenghtEncoder;
use ArithmeticError;

class BunnyHash
{
    protected string $capacity;
    protected string $modInv;

    function __construct(protected string $a, protected FixedLenghtEncoder $encoder, protected ?string $offset = '0')
    {
        $this->capacity = $encoder->getCapacity();

        if ($this->offset == null) {
            $this->offset = self::truncateDecimal(bcdiv($this->capacity, 2));
        }

        if (bccomp($this->capacity, $this->offset) <= 0) {
            throw new ArithmeticError('Offset is bigger than or equal capacity');
        }

        if (bccomp($this->offset, '0') == -1) {
            throw new ArithmeticError('Offset is negative');
        }

        $this->modInv = self::modInv($this->a, $this->capacity);
    }

    public function hash(string $input): string
    {
        if (bccomp($input, '0') == -1) {
            throw new ArithmeticError('Input cannot be negative');
        }
        $comp = bccomp($input, $this->capacity);
        if ($comp == 0 || $comp > 0) {
            throw new ArithmeticError('Input is too big to fit the hash lenght');
        }
        return $this->encoder->encodeInt(bcmod(bcadd(bcmul($this->a, $input), $this->offset), $this->capacity));
    }

    public function reverse(string $hash): string
    {
        if (strlen($hash) != $this->encoder->getLength()) {
            throw new ArithmeticError('Input has wrong length');
        }
        $x = $this->encoder->decodeInt($hash);
        return bcmod(bcmul(bcadd(bcsub($x, $this->offset), $this->capacity), $this->modInv), $this->capacity);
    }

    public function getCapacity(): string
    {
        return $this->capacity;
    }

    public function getOffset(): string
    {
        return $this->offset;
    }

    private static function modInv(string $number, string $modulus): string
    {
        $m0 = $modulus;
        $x0 = '0';
        $x1 = '1';

        if (bcmod($number, $modulus) == '0') {
            return null;
        }

        while (bccomp($number, '1') > 0) {
            $q = bcdiv($number, $modulus, 0);

            $temp = $modulus;
            $modulus = bcmod($number, $modulus);
            $number = $temp;

            $temp = $x0;
            $x0 = bcsub($x1, bcmul($q, $x0));
            $x1 = $temp;
        }

        if (bccomp($x1, '0') < 0) {
            $x1 = bcadd($x1, $m0);
        }

        return $x1;
    }

    private static function truncateDecimal(string $number): string
    {
        return explode('.', $number)[0];
    }
}
