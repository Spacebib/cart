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
     *
     * @param $currency
     * @param $amountInCent
     */
    private function __construct($currency, $amountInCent)
    {
        if (!is_numeric($amountInCent) || (floor($amountInCent) != $amountInCent) || $amountInCent < 0) {
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

    public static function fromDollar($currency, $amountInDollar)
    {
        return new self(
            $currency,
            intval(str_replace(',', '', $amountInDollar) * 100)
        );
    }

    public function toDollar()
    {
        return $this->amountInCent / 100;
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
        $this->canCalculate($b);

        return Money::fromCent(
            $this->getCurrency(),
            $this->toCent() + $b->toCent()
        );
    }

    public function minus(Money $b)
    {
        $this->canCalculate($b);

        $amount = ($this->toCent() - $b->toCent()) > 0 ? $this->toCent() - $b->toCent() : 0;

        return Money::fromCent(
            $this->getCurrency(),
            $amount
        );
    }

    public function product($p)
    {
        $amount = $this->toCent() * $p;

        return Money::fromCent($this->getCurrency(), intval($amount));
    }

    private function canCalculate(Money $b)
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
    }
}
