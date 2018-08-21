<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 14/3/18
 * Time: 11:35 AM
 */

namespace Dilab\Cart\Test;

use Dilab\Cart\Cart;
use Dilab\Cart\Coupons\Coupon;
use Dilab\Cart\Coupons\DiscountType;
use Dilab\Cart\Money;
use Dilab\Cart\Participant;
use Dilab\Cart\Registration;
use Dilab\Cart\Test\Factory\DonationFactory;
use Dilab\Cart\Test\Factory\EntitlementFactory;
use Dilab\Cart\Test\Factory\EventFactory;
use Dilab\Cart\Test\Factory\FormDataFactory;
use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{
    /**
     * @var Cart $cart
     */
    public $cart;

    public function setUp()
    {
        parent::setUp();

        $event = EventFactory::create();
        $this->cart = new Cart(
            'xuding@spacebib.com',
            $event
        );
    }

    public function test_add_ticket_then_get_participants()
    {
        $this->assertCount(0, $this->cart->tickets());

        $this->cart->addTicket(EventFactory::create()->getCategoryById(1), 2);
        $this->cart->addTicket(EventFactory::create()->getCategoryById(1), 2);
        $result = $this->cart->tickets();
        $this->assertCount(4, $result);

        $this->cart->addTicket(EventFactory::create()->getCategoryById(2), 2);
        $tickets = $this->cart->tickets();
        $this->assertCount(6, $tickets);

        $participants = $this->cart->getParticipants();
        $this->assertCount(12, $participants);

        // every participant is different
        $participantsObjects = array_map(function (Participant $participant) {
            return spl_object_hash($participant);
        }, $participants);
        $this->assertCount(12, array_unique($participantsObjects));

        // trackId is ordered
        $participantsTrackIds = array_map(function (Participant $participant) {
            return $participant->getTrackId();
        }, $participants);
        $expected = range(0, 11);
        $this->assertSame($expected, $participantsTrackIds);

        $this->assertNotNull($participants[0]->getGroupNum());
        $this->assertNotNull($participants[0]->getAccessCode());
    }

    public function testSubTotal()
    {
        $result = $this->cart->subTotal();
        $this->assertEquals(0, $result->toCent());

        $this->cart->addTicket(EventFactory::create()->getCategoryById(1), 1);
        $this->cart->addTicket(EventFactory::create()->getCategoryById(2), 2);
        $result = $this->cart->subTotal();
        $this->assertFalse($this->cart->hasDonation());
        $this->assertEquals(Money::fromCent('SGD', 101000), $result);

        $registration = new Registration($this->cart->getParticipants());
        $data = [
            'form' => FormDataFactory::correctData(),
            'entitlements' => EntitlementFactory::postData(),
            'donations' => DonationFactory::postData()

        ];
        $registration->fillParticipant(0, $data);
        $registration->fillParticipant(1, $data);
        $donation = $this->cart->donationTotal();
        $this->assertTrue($this->cart->hasDonation());
        $this->assertEquals(Money::fromCent('SGD', 2000), $donation);
    }

    public function testTotal()
    {
        $result = $this->cart->total();
        $this->assertEquals(0, $result->toCent());

        $this->cart->addTicket(EventFactory::create()->getCategoryById(2), 2);
        $result = $this->cart->total();
        // service = 100000*0.1 + 4*1000 = 10000 + 4000 = 14000
        $this->assertEquals(Money::fromCent('SGD', 114000), $result);
    }

    public function testCouponDiscount()
    {
        // price 1000*2
        $this->cart->addTicket(EventFactory::create()->getCategoryById(1), 2);
        //price 50000*2
        $this->cart->addTicket(EventFactory::create()->getCategoryById(2), 2);
        $result = $this->cart->total();
        // service fee = 102000*0.1 + 8*1000 = 10200+8000 = 18200
        $this->assertEquals(Money::fromCent('SGD', 120200), $result);

        // apply coupon
        $coupon = new Coupon(
            1,
            [1, 2],
            DiscountType::FIXVALUE,
            10,
            1101,
            10
        );

        $this->assertTrue($this->cart->setCoupon($coupon)->applyCoupon());
        // service fee =  (102000-10*4)*0.1 + 8*1000 = 10160 + 8000 = 18196
        $this->assertEquals(102000+18196-40, $this->cart->total()->toCent());
        $this->assertEquals(40, $this->cart->getDiscount()->toCent());
        $this->assertEquals(4, $this->cart->usedCouponQuantity());
        // cancel coupon
        $this->assertTrue($this->cart->setCoupon(null)->cancelCoupon());
        $this->assertEquals(102000+18200, $this->cart->total()->toCent());
        $this->assertEquals(0, $this->cart->getDiscount()->toCent());
        $this->assertEquals(0, $this->cart->usedCouponQuantity());
    }

    public function testProducts()
    {
        $event = EventFactory::create();

        // add ticket
        $this->cart->addTicket($event->getCategoryById(2), 2);
        // service_fee = 100000 * 0.1 + 4*1000 = 10000+4000 = 140000
        $this->assertEquals(Money::fromCent('SGD', 114000), $this->cart->total());

        // add products
        $product = $event->getProductById(1)->setSelectedVariantId(1);
        $this->cart->addProduct($product);
        // service_fee = 140000 + 100*0.1 = 140010
        $this->assertEquals(Money::fromCent('SGD', 114110), $this->cart->total());

        $this->cart->addProduct($product);
        $this->assertEquals(Money::fromCent('SGD', 114220), $this->cart->total());
        // remove products
        $this->cart->removeProduct(1, 1);
        $this->assertEquals(Money::fromCent('SGD', 114110), $this->cart->total());

        $this->cart->removeProduct(1, 1);
        $this->assertEquals(Money::fromCent('SGD', 114000), $this->cart->total());
    }

    /**
     * test coupon wont't over use
     */
    public function test_will_apply_the_most_expensive_ticket_when_coupon_only_one()
    {
        // price 1000
        $this->cart->addTicket(EventFactory::create()->getCategoryById(2), 2);
        //price 100000
        $this->cart->addTicket(EventFactory::create()->getCategoryById(1), 2);
        // service fee = 102000*0.1 + 8*1000 = 10200+8000 = 18200

        // apply coupon
        $coupon = new Coupon(
            1,
            [1, 2],
            DiscountType::FIXVALUE,
            10000,
            1101,
            1
        );
        // service fee =  (102000-10000)*0.1 + 8*1000 = 9200 + 8000 = 17200
        $this->assertTrue($this->cart->setCoupon($coupon)->applyCoupon());
        $this->assertEquals(102000+17200-10000, $this->cart->total()->toCent());
        $this->assertEquals(10000, $this->cart->getDiscount()->toCent());
        $this->assertEquals(1, $this->cart->usedCouponQuantity());
    }

    /**
     * https://github.com/Spacebib/starpodium/issues/171
     *  coupon is applied per category basis.
     *  e.g
     *   coupon discount = 50 (applied to category A) ,
     *   category A = 40,
     *   category B = 30,
     *   when buying three tickets (2 x A + 1 x B), it should only deduct 80 (40x2)
     */
    public function test_issue_171()
    {
        // price 50000
        $this->cart->addTicket(EventFactory::create()->getCategoryById(2), 1);
        //price 1000*2
        $this->cart->addTicket(EventFactory::create()->getCategoryById(1), 2);
        // service fee = 52000*0.1 + 6*1000 = 5200+6000 = 11200

        // apply coupon
        $coupon = new Coupon(
            1,
            [1],
            DiscountType::FIXVALUE,
            10000,
            1101,
            2
        );
        // service fee =  (52000-2000)*0.1 + 6*1000 = 5000 + 6000 = 11000
        $this->assertTrue($this->cart->setCoupon($coupon)->applyCoupon());
        $this->assertEquals(52000+11000-1000*2, $this->cart->total()->toCent());
        $this->assertEquals(2000, $this->cart->getDiscount()->toCent());
        $this->assertEquals(2, $this->cart->usedCouponQuantity());
    }

    public function test_should_not_charge_for_service_fee_when_total_with_discount_is_0()
    {
        // price 1000*1
        $this->cart->addTicket(EventFactory::create()->getCategoryById(1), 1);

        // apply coupon
        $coupon = new Coupon(
            1,
            [1, 2],
            DiscountType::PERCENTAGEOFF,
            100,
            1101,
            10
        );

        $this->assertTrue($this->cart->setCoupon($coupon)->applyCoupon());

        $this->assertEquals(1000, $this->cart->subTotal()->toCent());
        $this->assertEquals(0, $this->cart->subtotalAfterDiscount()->toCent());
        $this->assertEquals(0, $this->cart->calcServiceFee()->toCent());
        $this->assertEquals(0, $this->cart->total()->toCent());
    }
}
