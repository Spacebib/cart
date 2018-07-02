<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 14/3/18
 * Time: 9:35 AM
 */

namespace Dilab\Cart;

use Dilab\Cart\Coupons\Coupon;

class Category
{
    private $id;

    private $name;

    private $price;

    private $participants;

    private $originalPrice;

    /**
     * Category constructor.
     *
     * @param $id
     * @param $name
     * @param $price
     * @param $participants
     */
    public function __construct($id, $name, Money $price, $participants)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->participants = $participants;
        $this->originalPrice = $price;
    }

    public function applyCoupon(Coupon $coupon)
    {
        if (! $coupon->canApply($this->id)) {
            return false;
        }

        $this->price = $coupon->apply($this->originalPrice);

        return true;
    }

    public function cancelCoupon()
    {
        $this->price = $this->originalPrice;

        return true;
    }

    public function getDiscount()
    {
        return $this->originalPrice->minus($this->price);
    }

    public function isDiscounted()
    {
        return $this->getDiscount()->toCent() > 0;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Money
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return mixed
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * @return Money
     */
    public function getOriginalPrice(): Money
    {
        return $this->originalPrice;
    }
}
