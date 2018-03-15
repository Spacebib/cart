<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 14/3/18
 * Time: 12:03 PM
 */

namespace Dilab\Cart\Test;

use Dilab\Cart\Cart;
use Dilab\Cart\DataStore;
use Dilab\Cart\Registration;
use Dilab\Cart\Test\Factory\EventFactory;

class RegistrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var  Registration
     */
    private $registration;

    public function setUp()
    {
        parent::setUp();

        $mockDataStore = $this->getMockBuilder(DataStore::class)->getMock();

        $cart = new Cart('xuding@spacebib.com', $mockDataStore);

        $cart->addTicket(EventFactory::create()->getCategoryById(1), 1);

        $this->registration = new Registration(
            $cart->getParticipants(),
            $mockDataStore
        );
    }

    public function testFormFiller()
    {
        $trackId = 1;
        $expected = [
            'email' => '',
            'dob' => '',
            'first_name' => '',
            'last_name' => '',
            'nationality' => '',
            'name_on_bib' => ''
        ];
        $this->assertEquals($expected, $this->registration->renderParticipantForm($trackId));
        $this->assertFalse($this->registration->isDirty($trackId));
        $this->assertFalse($this->registration->isCompleted($trackId));
        $this->assertTrue($this->registration->isTouched($trackId));

        $trackId = 0;
        $expected = [
            'email' => '',
            'dob' => '',
            'first_name' => '',
            'last_name' => ''
        ];
        $result = $this->registration->renderParticipantForm($trackId);
        $this->assertEquals($expected, $result);
        $this->assertFalse($this->registration->isDirty($trackId));
        $this->assertFalse($this->registration->isCompleted($trackId));
        $this->assertTrue($this->registration->isTouched($trackId));

        $trackId = 1;
        $data = [
            'email' => 'xuding@spacebib.com',
            'dob' => '2018-01-02',
            'first_name' => 'xu',
            'last_name' => 'ding',
            'nationality' => 'CHINA',
            'name_on_bib' => 'Xu Ding'
        ];
        $this->registration->renderParticipantForm($trackId);
        $this->registration->fillParticipantForm($trackId, $data);
        $result = $this->registration->renderParticipantForm($trackId);
        $this->assertEquals($data, $result);
        $this->assertTrue($this->registration->isDirty($trackId));
    }

    public function testRedirectTo()
    {
        $trackId = 0;
        $data = [
            'email' => 'xuding@spacebib.com',
            'dob' => '2018-01-02',
            'first_name' => 'xu',
            'last_name' => 'ding'
        ];
        $this->assertFalse($this->registration->isCompleted($trackId));
        $this->registration->renderParticipantForm($trackId);
        $this->assertTrue( $this->registration->fillParticipantForm($trackId, $data));
        $this->assertTrue($this->registration->isCompleted($trackId));
        $this->assertEquals(1,$this->registration->redirectTo());

        $trackId = 2;
        $data = [
            'email' => 'xuding@spacebib.com',
            'dob' => '2018-01-02',
            'first_name' => 'xu',
            'last_name' => 'ding'
        ];
        $this->assertFalse($this->registration->isCompleted($trackId));
        $this->registration->renderParticipantForm($trackId);
        $this->registration->fillParticipantForm($trackId, $data);
        $this->assertTrue( $this->registration->fillParticipantForm($trackId, $data));
        $this->assertTrue($this->registration->isCompleted($trackId));
        $this->assertEquals(1,$this->registration->redirectTo());

    }

}
