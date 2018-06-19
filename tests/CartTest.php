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

    public function testAddTicketThenGetParticipants()
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

        $participantsObjects = array_map(function (Participant $participant) {
            return spl_object_hash($participant);
        }, $participants);
        $this->assertCount(12, array_unique($participantsObjects));

        $participantsTrackIds = array_map(function (Participant $participant) {
            return $participant->getTrackId();
        }, $participants);
        $expected = range(0, 11);
        $this->assertSame($expected, $participantsTrackIds);
    }

    public function testSubTotal()
    {
        $result = $this->cart->subTotal();
        $this->assertNull($result);

        $this->cart->addTicket(EventFactory::create()->getCategoryById(1), 1);
        $this->cart->addTicket(EventFactory::create()->getCategoryById(2), 2);
        $result = $this->cart->subTotal();
        $this->assertFalse($this->cart->hasDonation());
        $this->assertEquals(Money::fromCent('SGD', 101000), $result);

        $registration = new Registration($this->cart->getParticipants());
        $data = [
            'form' => FormDataFactory::correctData(),
            'entitlements' => EntitlementFactory::postData(),
            'donation' => DonationFactory::postData()

        ];
        $registration->fillParticipant(0, $data);
        $registration->fillParticipant(1, $data);
        $donation = $this->cart->donation();
        $this->assertTrue($this->cart->hasDonation());
        $this->assertEquals(Money::fromCent('SGD', 20), $donation);
    }

    public function testTotal()
    {
        $result = $this->cart->total();
        $this->assertNull($result);

        $this->cart->addTicket(EventFactory::create()->getCategoryById(2), 2);
        $result = $this->cart->total();
        $this->assertEquals(Money::fromCent('SGD', 100000), $result);
    }

    public function testCouponDiscount()
    {
        // price 1000
        $this->cart->addTicket(EventFactory::create()->getCategoryById(2), 2);
        //price 10000
        $this->cart->addTicket(EventFactory::create()->getCategoryById(1), 2);
        $result = $this->cart->total();
        $this->assertEquals(Money::fromCent('SGD', 102000), $result);

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
        $this->assertEquals(102000-40, $this->cart->total()->toCent());
        $this->assertEquals(40, $this->cart->getDiscount()->toCent());
        $this->assertEquals(4, $this->cart->usedCouponQuantity());
        // cancel coupon
        $this->assertTrue($this->cart->setCoupon(null)->cancelCoupon());
        $this->assertEquals(102000, $this->cart->total()->toCent());
        $this->assertEquals(0, $this->cart->getDiscount()->toCent());
        $this->assertEquals(0, $this->cart->usedCouponQuantity());
    }

    public function testProducts()
    {
        // add ticket
        $this->cart->addTicket(EventFactory::create()->getCategoryById(2), 2);
        $this->assertEquals(Money::fromCent('SGD', 100000), $this->cart->total());

        // add products
        $event = EventFactory::create();
        $product = $event->getProductById(1)->setSelectedVariantId(1);
        $this->cart->addProduct($product);
        $this->assertEquals(Money::fromCent('SGD', 100100), $this->cart->total());

        $this->cart->addProduct($product);
        $this->assertEquals(Money::fromCent('SGD', 100200), $this->cart->total());
        // remove products
        $this->cart->removeProduct(1, 1);
        $this->assertEquals(Money::fromCent('SGD', 100100), $this->cart->total());

        $this->cart->removeProduct(1, 1);
        $this->assertEquals(Money::fromCent('SGD', 100000), $this->cart->total());
    }

    public function test_will_apply_the_most_expensive_ticket_when_coupon_only_one()
    {
        // price 1000
        $this->cart->addTicket(EventFactory::create()->getCategoryById(2), 2);
        //price 100000
        $this->cart->addTicket(EventFactory::create()->getCategoryById(1), 2);

        // apply coupon
        $coupon = new Coupon(
            1,
            [1, 2],
            DiscountType::FIXVALUE,
            10000,
            1101,
            1
        );

        $this->assertTrue($this->cart->setCoupon($coupon)->applyCoupon());
        $this->assertEquals(102000-10000, $this->cart->total()->toCent());
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

        // apply coupon
        $coupon = new Coupon(
            1,
            [1],
            DiscountType::FIXVALUE,
            10000,
            1101,
            2
        );

        $this->assertTrue($this->cart->setCoupon($coupon)->applyCoupon());
        $this->assertEquals(52000-1000*2, $this->cart->total()->toCent());
        $this->assertEquals(2000, $this->cart->getDiscount()->toCent());
        $this->assertEquals(2, $this->cart->usedCouponQuantity());
    }
}
