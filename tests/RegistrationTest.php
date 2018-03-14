<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 14/3/18
 * Time: 12:03 PM
 */

namespace Dilab\Cart\Test;

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
        $this->registration = new Registration(
            EventFactory::create()->getParticipants(),
            $this->getMockBuilder(DataStore::class)->getMock()
        );
    }

    public function testRenderParticipant()
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
        $this->assertEquals($expected, $this->registration->renderFormData($trackId));
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
        $result = $this->registration->renderFormData($trackId);
        $this->assertEquals($expected, $result);
        $this->assertFalse($this->registration->isDirty($trackId));
        $this->assertFalse($this->registration->isCompleted($trackId));
        $this->assertTrue($this->registration->isTouched($trackId));
    }

    public function testFillParticipant()
    {

    }

    public function testIsRedirect()
    {

    }

}
