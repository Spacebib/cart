<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/4/12
 * Time: 下午3:35
 */

namespace Dilab\Cart\Test;

use Dilab\Cart\Coupon;
use Dilab\Cart\DiscountType;
use Dilab\Cart\Money;

class CouponTest extends \PHPUnit_Framework_TestCase
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

    public function testApply()
    {
        $originPrice = Money::fromCent('VHD', 1000);

        $discountedPrice = $this->coupon->apply($originPrice);

        $this->assertEquals(990, $discountedPrice->toCent());
        // Percentage Off
        $this->coupon = new Coupon(
            1,
            [1],
            DiscountType::PERCENTAGEOFF,
            90,
            '1101'
        );

        $discountedPrice = $this->coupon->apply($originPrice);

        $this->assertEquals(intval(1000*0.9), $discountedPrice->toCent());
        // fix value discount above of original price, should return 0
        $this->coupon = new Coupon(
            1,
            [1],
            DiscountType::FIXVALUE,
            1000000,
            '1101'
        );
        $discountedPrice = $this->coupon->apply($originPrice);
        $this->assertEquals(0, $discountedPrice->toCent());
    }
}
