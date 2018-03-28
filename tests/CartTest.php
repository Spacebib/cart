<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 14/3/18
 * Time: 11:35 AM
 */

namespace Dilab\Cart\Test;

use Dilab\Cart\Cart;
use Dilab\Cart\Category;
use Dilab\Cart\DataStore;
use Dilab\Cart\Form;
use Dilab\Cart\Money;
use Dilab\Cart\Participant;
use Dilab\Cart\Registration;
use Dilab\Cart\Test\Factory\DonationFactory;
use Dilab\Cart\Test\Factory\EntitlementFactory;
use Dilab\Cart\Test\Factory\EventFactory;
use Dilab\Cart\Test\Factory\FormDataFactory;

class CartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Cart $cart
     */
    public $cart;

    public function setUp()
    {
        parent::setUp();

        $this->cart = new Cart(
            'xuding@spacebib.com',
            $this->getMockBuilder(DataStore::class)->getMock()
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

    public function testSerialization()
    {
        $this->cart->addTicket(EventFactory::create()->getCategoryById(1), 2);
        $this->cart->addTicket(EventFactory::create()->getCategoryById(1), 2);
        $this->cart->addTicket(EventFactory::create()->getCategoryById(2), 2);

        $serialized = $this->cart->serialize();
        $unserialized = Cart::deserialize($serialized);
        $this->assertEquals($unserialized, $this->cart);
    }
}
