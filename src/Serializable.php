<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 15/3/18
 * Time: 2:32 PM
 */

namespace Dilab\Cart;


interface Serializable
{
    public function serialize();

    public static function deserialize($data);
}