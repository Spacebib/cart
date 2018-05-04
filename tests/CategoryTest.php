<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/4/12
 * Time: 下午3:54
 */

namespace Dilab\Cart\Test;

use Dilab\Cart\Category;
use Dilab\Cart\Coupon;
use Dilab\Cart\DiscountType;
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

    public function testApplyCoupon()
    {
        $coupon = new Coupon(
            1,
            [2],
            DiscountType::FIXVALUE,
            10,
            '1101'
        );

        $this->assertFalse($this->category->applyCoupon($coupon));
        $coupon->setCategoryIds([1, 2]);
        $this->assertSame(1000, $this->category->getPrice()->toCent());
        $this->assertTrue($this->category->applyCoupon($coupon));
        $this->assertSame(990, $this->category->getPrice()->toCent());
    }
}
