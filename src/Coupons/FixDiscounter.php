<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/7/13
 * Time: 上午10:49
 */

namespace Dilab\Cart\Coupons;

use Dilab\Cart\Money;

class FixDiscounter implements Discounter
{
    private $type;

    private $rate;
    /** @var  Money */
    private $discount;
    /** @var  Money */
    private $discountedPrice;

    /**
     * FixDiscount constructor.
     * @param string $type
     * @param int $rate
     */
    public function __construct(string $type, int $rate)
    {
        $this->type = $type;
        $this->rate = $rate;
    }

    public function execute(Money $amount)
    {
        $this->guard($amount);

        $currency = $amount->getCurrency();

        $this->discount = Money::fromCent($currency, $this->rate);

        $this->discountedPrice = $amount->minus(Money::fromCent($currency, $this->rate));
    }

    private function guard(Money $amount)
    {
        if ($this->rate > $amount->toCent()) {
            $this->rate = $amount->toCent();
        }
    }

    /**
     * @return Money
     */
    public function getDiscount(): Money
    {
        return $this->discount;
    }

    /**
     * @return Money
     */
    public function getDiscountedPrice(): Money
    {
        return $this->discountedPrice;
    }
}
