<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/4/12
 * Time: 下午3:35
 */

namespace Dilab\Cart\Test\Coupons;

use Dilab\Cart\Coupons\Coupon;
use Dilab\Cart\Coupons\DiscountType;
use Dilab\Cart\Exceptions\InvalidDiscountTypeException;
use Dilab\Cart\Money;
use PHPUnit\Framework\TestCase;

class CouponTest extends TestCase
{
    /** @var  Coupon */
    private $coupon;

    public function setUp()
    {
        $this->coupon = new Coupon(
            1,
            [1, 2],
            DiscountType::FIXVALUE,
            10,
            '1101'
        );
    }

    public function test_fixed_discount()
    {
        $originPrice = Money::fromCent('VHD', 1000);

        $discountedPrice = $this->coupon->apply($originPrice);

        $this->assertEquals(10, $this->coupon->getDiscount()->toCent());
        $this->assertEquals(990, $discountedPrice->toCent());
    }

    public function test_percentage_off()
    {
        $originPrice = Money::fromCent('VHD', 1000);

        $this->coupon = new Coupon(
            1,
            [1],
            DiscountType::PERCENTAGEOFF,
            90,
            '1101'
        );

        $discountedPrice = $this->coupon->apply($originPrice);

        $this->assertEquals(900, $this->coupon->getDiscount()->toCent());
        $this->assertEquals(intval(1000 - 1000*0.9), $discountedPrice->toCent());
    }

    public function test_fixed_discount_above_of_original_price()
    {
        $originPrice = Money::fromCent('VHD', 1000);

        // fix value discount above of original price, should return 0
        $this->coupon = new Coupon(
            1,
            [1],
            DiscountType::FIXVALUE,
            1000000,
            '1101'
        );
        $discountedPrice = $this->coupon->apply($originPrice);

        $this->assertEquals(1000, $this->coupon->getDiscount()->toCent());
        $this->assertEquals(0, $discountedPrice->toCent());
    }
    
    public function test_validate_discount_type()
    {
        $originPrice = Money::fromCent('VHD', 1000);

        $this->coupon = new Coupon(
            1,
            [1],
            'fdfd',
            1000000,
            '1101'
        );
        
        $this->expectException(InvalidDiscountTypeException::class);
        $this->coupon->apply($originPrice);
    }
}
