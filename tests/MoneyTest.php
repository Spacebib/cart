<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 14/3/18
 * Time: 10:45 AM
 */

namespace Dilab\Cart\Test;

use Dilab\Cart\Money;

class MoneyTest extends \PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        $money = Money::fromCent('SGD',10000);
        $this->assertInstanceOf(Money::class, $money);
        $this->assertSame(10000, $money->toCent());
    }
}
