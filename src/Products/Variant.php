<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/4/17
 * Time: 上午9:43
 */

namespace Dilab\Cart\Products;

use Dilab\Cart\Money;

class Variant
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';

    private $id;

    private $status;

    private $name;

    private $stock;

    private $price;

    private $selected = false;

    public function __construct($id, $name, $stock, $status, Money $price, $selected = false)
    {
        $this->id = $id;
        $this->name = $name;
        $this->stock = $stock;
        $this->status = $status;
        $this->price = $price;
        $this->selected = $selected;
    }

    public function hasStock()
    {
        return $this->stock > 0;
    }

    public function isAvailable()
    {
        return $this->hasStock() && $this->status == self::ACTIVE;
    }

    /**
     * @return mixed
     */
    public function getSelected()
    {
        return $this->selected;
    }

    /**
     * @param mixed $selected
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;
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
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return Money
     */
    public function getPrice(): Money
    {
        return $this->price;
    }

    /**
     * @param Money $price
     */
    public function setPrice(Money $price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getStock()
    {
        return $this->stock;
    }
}
