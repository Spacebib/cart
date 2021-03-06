<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/22
 * Time: 上午10:36
 */

namespace Dilab\Cart\Test\Rules;

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

        $event = EventFactory::create();

        $cart = new Cart('xuding@spacebib.com', $event);

        $cart->addTicket($event->getCategoryById(1), 2);

        $this->registration = new Registration($cart->getParticipants());
    }

    public function testValid()
    {
        $trackId = 0;
        $participant = $this->registration->getParticipantByTrackId($trackId);
        $data = FormDataFactory::correctData();
        $this->registration->renderForm($participant);
        $this->assertTrue($this->registration->fillForm($participant, $data));

        $trackId = 1;
        $participant = $this->registration->getParticipantByTrackId($trackId);
        $data = FormDataFactory::correctData();
        $this->registration->renderForm($participant);
        $this->assertTrue($this->registration->fillForm($participant, $data));

        $trackId = 2;
        $participant = $this->registration->getParticipantByTrackId($trackId);

        $data = FormDataFactory::correctData();
        $this->registration->renderForm($participant);
        $this->assertFalse($this->registration->fillForm($participant, $data));

        $trackId = 3;
        $participant = $this->registration->getParticipantByTrackId($trackId);
        $data = FormDataFactory::correctData();
        $this->registration->renderForm($participant);
        $this->assertTrue($this->registration->fillForm($participant, $data));
    }

    public function tearDown()
    {
        $this->registration=null;
    }
}
