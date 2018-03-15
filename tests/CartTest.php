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
use Dilab\Cart\Test\Factory\EventFactory;

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
        $this->assertEquals(Money::fromCent('SGD', 101000), $result);
    }

    public function testTotal()
    {
        $result = $this->cart->total();
        $this->assertNull($result);

        $this->cart->addTicket(EventFactory::create()->getCategoryById(2), 2);
        $result = $this->cart->total();
        $this->assertEquals(Money::fromCent('SGD', 100000), $result);
    }
}
