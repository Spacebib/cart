<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 14/3/18
 * Time: 9:35 AM
 */

namespace Dilab\Cart;

class Category
{
    private $id;

    private $name;

    private $price;

    private $participants;

    private $originalPrice;

    /**
     * Category constructor.
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
        if (!in_array($this->id, $coupon->getCategoryIds())) {
            return false;
        }
        $this->price = $coupon->apply($this->originalPrice);
        return true;
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
}
