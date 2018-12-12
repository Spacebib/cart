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
            $cent/100,
            $dollar
        );
        
        $money = Money::fromDollar($currency, $dollar);
        $this->assertEquals($cent, $money->toCent());
    }

    public function testProductWillRoundUp()
    {
        $moneyA = Money::fromCent('SGD', 555);
        $p = 0.12;
        $result = $moneyA->product($p);
        $this->assertEquals(Money::fromCent('SGD', 67), $result);
    }
}
