<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 14/3/18
 * Time: 9:48 AM
 */

namespace Dilab\Cart;

class Money
{
    private $currency;

    private $amountInCent;

    /**
     * Money constructor.
     * @param $currency
     * @param $amountInCent
     */
    private function __construct($currency, $amountInCent)
    {
        if ($amountInCent < 0) {
            throw new \LogicException('Invalid cent value');
        }

        $this->currency = $currency;
        $this->amountInCent = $amountInCent;
    }

    public static function fromCent($currency, $amountInCent)
    {
        return new self(
            $currency,
            $amountInCent
        );
    }

    public function toCent()
    {
        return $this->amountInCent;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    public function plus(Money $b)
    {
        if ($b->getCurrency() !== $this->getCurrency()) {
            throw new \LogicException(
                sprintf(
                    'Invalid plus operation between two different currencies, %s, %s',
                    $b->getCurrency(),
                    $this->getCurrency()
                )
            );
        }

        return Money::fromCent(
            $this->getCurrency(),
            $this->toCent() + $b->toCent()
        );
    }
}
