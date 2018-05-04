<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 14/3/18
 * Time: 10:45 AM
 */

namespace Dilab\Cart\Test;

use Dilab\Cart\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function testInit()
    {
        $money = Money::fromCent('SGD', 10000);
        $this->assertInstanceOf(Money::class, $money);
        $this->assertSame(10000, $money->toCent());
    }

    public function testPlus()
    {
        $moneyA = Money::fromCent('SGD', 10000);
        $moneyB = Money::fromCent('SGD', 50);
        $result = $moneyA->plus($moneyB);
        $this->assertEquals(Money::fromCent('SGD', 10050), $result);

        $this->expectException(\LogicException::class);
        $moneyB = Money::fromCent('USD', 50);
        $moneyA->plus($moneyB);
    }

    public function testDollar()
    {
        $currency = 'USD';
        $cent = 10000100000;
        $money = Money::fromCent($currency, $cent);
        $dollar = $money->toDollar();
        $this->assertEquals(
            number_format($cent/100, 2, '.', ','),
            $dollar
        );
        
        $money = Money::fromDollar($currency, $dollar);
        $this->assertEquals($cent, $money->toCent());
    }
}
