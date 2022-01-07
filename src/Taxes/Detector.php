<?php

namespace App\Taxes;

class Detector
{
    protected $amount;

    public function __construct(float $amount)
    {
        $this->amount = $amount;
    }

    public function detect(float $prix) : bool
    {
        if ($prix > 0 && $prix <= $this->amount) {
            return false;
        } else {
            return true;
        }
    }
}
