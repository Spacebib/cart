<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/4/12
 * Time: 下午3:54
 */

namespace Dilab\Cart\Test;

use Dilab\Cart\Category;
use Dilab\Cart\Coupons\Coupon;
use Dilab\Cart\Coupons\DiscountType;
use Dilab\Cart\Money;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    /** @var  Category */
    private $category;

    public function setUp()
    {
        $this->category = new Category(
            1,
            'test',
            Money::fromCent('VHD', 1000),
            []
        );
    }

    public function test_apply_coupon()
    {
        $coupon = new Coupon(
            1,
            [2],
            DiscountType::FIXVALUE,
            10,
            '1101',
            10
        );

        $this->assertFalse($this->category->applyCoupon($coupon));
        $coupon->setCategoryIds([1, 2]);
        $this->assertSame(1000, $this->category->getPrice()->toCent());
        $this->assertTrue($this->category->applyCoupon($coupon));
        $this->assertSame(990, $this->category->getPrice()->toCent());
    }

    public function test_cancel_coupon()
    {
        $this->test_apply_coupon();

        $this->category->cancelCoupon();

        $this->assertSame(1000, $this->category->getPrice()->toCent());
    }

    public function test_get_discount()
    {
        $currency = $this->category->getPrice()->getCurrency();

        $this->assertEquals(Money::fromCent($currency, 0), $this->category->getDiscount());

        $this->test_apply_coupon();

        $discount = $this->category->getDiscount();

        $expected = Money::fromCent($currency, 10);

        $this->assertEquals($expected, $discount);
    }

    public function test_is_discounted()
    {
        $this->assertFalse($this->category->isDiscounted());

        $this->test_apply_coupon();

        $this->assertTrue($this->category->isDiscounted());
    }
}
