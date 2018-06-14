<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/4/12
 * Time: 下午3:13
 */

namespace Dilab\Cart\Coupons;

class DiscountType
{
    const FIXVALUE = 'fixed';
    const PERCENTAGEOFF = 'percentage';

    public static function types()
    {
        $oClass = new \ReflectionClass(__CLASS__);

        return $oClass->getConstants();
    }
}
