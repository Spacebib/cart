<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 14/3/18
 * Time: 9:56 AM
 */

namespace Dilab\Cart;


class Form
{
    private $fields;

    /**
     * Form constructor.
     * @param $fields
     */
    public function __construct($fields)
    {
        $this->fields = $fields;
    }


}