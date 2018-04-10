<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 14/3/18
 * Time: 12:03 PM
 */

namespace Dilab\Cart\Test;

use Dilab\Cart\Cart;
use Dilab\Cart\Registration;
use Dilab\Cart\Test\Factory\DonationFactory;
use Dilab\Cart\Test\Factory\EntitlementFactory;
use Dilab\Cart\Test\Factory\EventFactory;
use Dilab\Cart\Test\Factory\FormDataFactory;

class RegistrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var  Registration
     */
    private $registration;

    public function setUp()
    {
        parent::setUp();

        $cart = new Cart('xuding@spacebib.com');

        $cart->addTicket(EventFactory::create()->getCategoryById(1), 1);

        $this->registration = new Registration($cart->getParticipants());
    }

    public function testRenderParticipant()
    {
        $trackId = 0;

        $expected = [
            'form' => FormDataFactory::emptyData(),
            'entitlements' => EntitlementFactory::entitlements(),
            'fundraises' => DonationFactory::emptyDonation()
        ];
        $result = $this->registration->renderParticipant($trackId);
        $this->assertFalse($this->registration->isDirty($trackId));
        $this->assertFalse($this->registration->isCompleted($trackId));
        $this->assertTrue($this->registration->isTouched($trackId));
        $this->assertEquals($expected, $result);
    }

    public function testFillParticipant()
    {
        $trackId = 0;

        $data = [
            'form' => FormDataFactory::correctData(),
            'entitlements' => EntitlementFactory::postData(),
            'donation' => []
        ];
        $this->registration->renderParticipant($trackId);
        $this->assertFalse($this->registration->fillParticipant($trackId, $data));
        $this->assertFalse($this->registration->isCompleted($trackId));

        $data = [
            'form' => FormDataFactory::correctData(),
            'entitlements' => EntitlementFactory::postData(),
            'donation' => DonationFactory::postData()

        ];
        $this->registration->renderParticipant($trackId);
        $this->assertTrue($this->registration->fillParticipant($trackId, $data));
        $this->assertTrue($this->registration->isCompleted($trackId));
    }

    public function testRedirectTo()
    {
        $trackId = 0;
        $data = [
            'form' => FormDataFactory::correctData(),
            'entitlements' => EntitlementFactory::postData(),
            'donation' => DonationFactory::postData()
        ];
        $this->assertFalse($this->registration->isCompleted($trackId));
        $this->registration->renderParticipant($trackId);
        $this->assertTrue($this->registration->fillParticipant($trackId, $data));
        $this->assertTrue($this->registration->isCompleted($trackId));
        $this->assertEquals(1, $this->registration->redirectTo());

        $trackId = 1;
        $data = [
            'form' => FormDataFactory::correctData(),
            'entitlements' => [],
            'donation' => []
        ];
        $this->assertFalse($this->registration->isCompleted($trackId));
        $this->registration->renderParticipant($trackId);
        $this->assertTrue($this->registration->fillParticipant($trackId, $data));
        $this->assertTrue($this->registration->isCompleted($trackId));
        $this->assertEquals(Registration::SUMMARY, $this->registration->redirectTo());
    }

    public function testSerialization()
    {
        $trackId = 0;
        $data = FormDataFactory::correctData();
        $this->assertFalse($this->registration->isCompleted($trackId));
        $this->registration->renderParticipantForm($trackId);
        $this->assertTrue($this->registration->fillParticipantForm($trackId, $data));

        $serialized = $this->registration->serialize();
        $unserialized = Registration::deserialize($serialized);
        $this->assertEquals($unserialized, $this->registration);
    }

    public function testFillForm()
    {
        $trackId = 1;
        $expected = [
            'email' => '',
            'email_confirmation' => '',
            'dob' => ['day'=>'', 'month'=>'', 'year'=>''],
            'first_name' => '',
            'last_name' => '',
            'nationality' => '',
            'name_on_bib' => '',
            'gender' => '',
        ];
        $this->assertEquals($expected, $this->registration->renderParticipantForm($trackId));

        $trackId = 0;
        $expected = FormDataFactory::emptyData();
        $result = $this->registration->renderParticipantForm($trackId);
        $this->assertEquals($expected, $result);

        $trackId = 1;
        $data = [
            'email' => 'xuding@spacebib.com',
            'email_confirmation' => 'xuding@spacebib.com',
            'dob' => ['day'=>'02', 'month'=> '01', 'year'=>'1995'],
            'first_name' => 'xu',
            'last_name' => 'ding',
            'nationality' => 'CHINA',
            'name_on_bib' => 'Xu Ding',
            'gender' => 'male'
        ];
        $this->registration->renderParticipantForm($trackId);
        $this->assertTrue($this->registration->fillParticipantForm($trackId, $data));
        $result = $this->registration->renderParticipantForm($trackId);
        $this->assertEquals($data, $result);
    }

    public function testFillEntitlement()
    {
        $trackId = 0;
        $expected = EntitlementFactory::entitlements();
        $result = $this->registration->renderParticipantEntitlements($trackId);
        $this->assertEquals($expected, $result);

        $requestData = [];
        $result = $this->registration->fillParticipantsEntitlements($trackId, $requestData);
        $this->assertFalse($result);
        $this->assertArrayHasKey('entitlements', $this->registration->getErrors($trackId));

        $requestData = [
            1 => ''
        ];
        $result = $this->registration->fillParticipantsEntitlements($trackId, $requestData);
        $this->assertFalse($result);
        $this->assertArrayHasKey('entitlements', $this->registration->getErrors($trackId));

        $requestData = [
            1 => 1,
            2 => 3
        ];
        $result = $this->registration->fillParticipantsEntitlements($trackId, $requestData);
        $this->assertTrue($result);
    }
}
