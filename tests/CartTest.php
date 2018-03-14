<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 14/3/18
 * Time: 11:35 AM
 */

namespace Dilab\Cart\Test;

use Dilab\Cart\Cart;
use Dilab\Cart\DataStore;
use Dilab\Cart\Money;
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

    public function testAddTicket()
    {
        $this->assertCount(0, $this->cart->tickets());

        $this->cart->addTicket(EventFactory::create()->getCategoryById(1), 2);
        $this->cart->addTicket(EventFactory::create()->getCategoryById(1), 2);
        $result = $this->cart->tickets();
        $this->assertCount(4, $result);

        $this->cart->addTicket(EventFactory::create()->getCategoryById(2), 2);
        $result = $this->cart->tickets();
        $this->assertCount(6, $result);
    }

    public function testSubTotal()
    {
        $result = $this->cart->subTotal();
        $this->assertNull($result);

        $this->cart->addTicket(EventFactory::create()->getCategoryById(2), 2);
        $result = $this->cart->subTotal();
        $this->assertEquals(Money::fromCent('SGD', 100000), $result);
    }

    public function testTotal()
    {

    }
}
