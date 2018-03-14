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

    private $tickets;

    /**
     * Cart constructor.
     * @param $buyerEmail
     * @param DataStore $store
     */
    public function __construct($buyerEmail, DataStore $store)
    {
        $this->buyerEmail = $buyerEmail;
        $this->store = $store;
        $this->tickets = [];
    }

    public function addTicket(Category $category, $qty)
    {
        $tickets = array_fill(0, $qty, $category);
        $this->tickets = array_merge($this->tickets, $tickets);
        return $this;
    }

    public function tickets()
    {
        return $this->tickets;
    }

    public function subTotal()
    {
        if (empty($this->tickets)) {
            return null;
        }

        $currency = $this->tickets[0]->getPrice()->getCurrency();

        $ticketsSubTotal = array_reduce($this->tickets, function ($carry, Category $category) {
            return $category->getPrice()->plus($carry);
        }, Money::fromCent($currency, 0));

        return $ticketsSubTotal;
    }

    public function total()
    {
        return $this->subTotal();
    }

}