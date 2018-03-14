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
    /**
     * @var DataStore
     */
    private $store;

    /**
     * Cart constructor.
     * @param $store
     */
    public function __construct(DataStore $store)
    {
        $this->store = $store;
    }

    public function addTicket($categoryId, $qty)
    {

    }

}