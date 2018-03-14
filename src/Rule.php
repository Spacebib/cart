<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 14/3/18
 * Time: 11:10 AM
 */

namespace Dilab\Cart;


class Rule
{
    private $name;

    private $condition;

    /**
     * Rule constructor.
     * @param $name
     * @param $condition
     */
    public function __construct($name, $condition)
    {
        $this->name = $name;
        $this->condition = $condition;
    }


}