<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/5/22
 * Time: 下午2:48
 */

namespace Dilab\Cart\Exceptions;

class InvalidDiscountTypeException extends \LogicException
{
    public static function throw($msg)
    {
        throw new self($msg);
    }
}
