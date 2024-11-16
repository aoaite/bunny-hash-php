<?php

namespace Aoaite\BunnyHash;

use Aoaite\BunnyHash\Encodings\Encoding;
use ArithmeticError;

class BunnyHash
{
    protected string $n;
    protected string $modInv;

    function __construct(protected string $a, protected Encoding $encoder, protected string $offset = '0')
    {
        $this->n = bcpow($this->encoder->getBase(), $this->encoder->getLength());
        $this->modInv = self::modInv($this->a, $this->n);
    }

    public function hash(int $input): string
    {
        $x = strval($input);
        if ($input < 0) {
            throw new ArithmeticError('Input cannot be negative');
        }
        $comp = bccomp($x, $this->n);
        if ($comp == 0 || $comp > 0) {
            throw new ArithmeticError('Input is too big to fit the hash lenght');
        }
        return $this->encoder->encode(intval(bcmod(bcadd(bcmul($this->a, $x), $this->offset), $this->n)));
    }

    public function reverse(string $hash): int|null
    {
        if (strlen($hash) != $this->encoder->getLength()) {
            throw new ArithmeticError('Input has wrong length');
        }
        $x = strval($this->encoder->decode($hash));
        $result = bcmod(bcmul(bcadd(bcsub($x, $this->offset), $this->n), $this->modInv), $this->n);
        return intval($result);
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
}
