<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 13/3/18
 * Time: 5:55 PM
 */

namespace Dilab\Cart;


class Cart
{
    private $buyerEmail;

    /**
     * @var DataStore
     */
    private $store;

    /**
     * Cart constructor.
     * @param $buyerEmail
     * @param DataStore $store
     */
    public function __construct($buyerEmail, DataStore $store)
    {
        $this->buyerEmail = $buyerEmail;
        $this->store = $store;
    }

    public function addTicket(Category $category, $qty)
    {

    }

    public function tickets()
    {

    }

    public function subTotal()
    {

    }

    public function total()
    {

    }
}