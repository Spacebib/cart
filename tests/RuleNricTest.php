<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/22
 * Time: 上午10:36
 */

namespace Dilab\Cart\Test;

use Dilab\Cart\Cart;
use Dilab\Cart\Registration;
use Dilab\Cart\Rules\RuleNric;
use Dilab\Cart\Test\Factory\EventFactory;
use Dilab\Cart\Test\Factory\FormDataFactory;
use PHPUnit\Framework\TestCase;

class RuleNricTest extends TestCase
{
    /**
     * @var  Registration
     */
    private $registration;

    public function setUp()
    {
        parent::setUp();

        $cart = new Cart('xuding@spacebib.com');

        $cart->addTicket(EventFactory::create()->getCategoryById(1), 2);

        $this->registration = new Registration($cart->getParticipants());
    }

    public function testValid()
    {
        $trackId = 0;
        $data = FormDataFactory::correctData();
        $this->registration->renderParticipantForm($trackId);
        $this->assertTrue($this->registration->fillParticipantForm($trackId, $data));

        $trackId = 1;
        $data = FormDataFactory::correctData();
        $this->registration->renderParticipantForm($trackId);
        $this->assertTrue($this->registration->fillParticipantForm($trackId, $data));

        $trackId = 2;
        $data = FormDataFactory::correctData();
        $this->registration->renderParticipantForm($trackId);
        $this->assertFalse($this->registration->fillParticipantForm($trackId, $data));

        $trackId = 3;
        $data = FormDataFactory::correctData();
        $this->registration->renderParticipantForm($trackId);
        $this->assertTrue($this->registration->fillParticipantForm($trackId, $data));
    }

    public function tearDown()
    {
        $this->registration=null;
    }

}
