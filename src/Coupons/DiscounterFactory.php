<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/7/13
 * Time: 上午10:55
 */

namespace Dilab\Cart\Coupons;

use Dilab\Cart\Exceptions\InvalidDiscountTypeException;

class DiscounterFactory
{
    public static function build(string $type, $rate)
    {
        if (! in_array($type, DiscountType::types())) {
            InvalidDiscountTypeException::throw(
                sprintf('invalid discount type %s', $type)
            );
        }

        if ($type === DiscountType::PERCENTAGEOFF) {
            return new PercentageDiscounter($type, $rate);
        } else {
            return new FixDiscounter($type, $rate);
        }
    }
}
