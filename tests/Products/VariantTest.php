<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/5/17
 * Time: 上午10:10
 */

namespace Dilab\Cart\Test\Products;

use Dilab\Cart\Money;
use Dilab\Cart\Products\Variant;
use PHPUnit\Framework\TestCase;

class VariantTest extends TestCase
{
    public function test_is_available()
    {
        $price = Money::fromCent('HKD', 100);
        $variant = new Variant(1, 't', 1, 0, $price);

        $this->assertFalse($variant->isAvailable());

        $variant = new Variant(1, 't', 0, 1, $price);

        $this->assertFalse($variant->isAvailable());

        $variant = new Variant(1, 't', 1, 1, $price);

        $this->assertTrue($variant->isAvailable());
    }
}
