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
    public function __construct($currency, $amountInCent)
    {
        $this->currency = $currency;
        $this->amountInCent = $amountInCent;
    }

}