<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/7/13
 * Time: 上午10:49
 */

namespace Dilab\Cart\Coupons;

use Dilab\Cart\Money;

class PercentageDiscounter implements Discounter
{
    /**
     * @var string
     */
    private $type;
    /**
     * @var
     */
    private $rate;
    /**
     * @var Money
     */
    private $discount;
    /**
     * @var Money
     */
    private $discountedPrice;

    /**
     * PercentageDiscount constructor.
     * @param string $type
     * @param $rate
     */
    public function __construct(string $type, $rate)
    {
        $this->type = $type;
        $this->rate = $rate;
    }

    public function execute(Money $amount)
    {
        $this->guard();

        $currency = $amount->getCurrency();

        $this->discount = Money::fromCent($currency, $amount->toCent()*$this->rate/100);

        $this->discountedPrice = $amount->minus($this->discount);
    }

    private function guard()
    {
        if ($this->rate > 100) {
            $this->rate = 100;
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
