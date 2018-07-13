<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/7/13
 * Time: 上午10:49
 */

namespace Dilab\Cart\Coupons;

use Dilab\Cart\Money;

interface Discounter
{
    public function execute(Money $amount);
}
