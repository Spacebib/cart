<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 14/3/18
 * Time: 12:03 PM
 */

namespace Dilab\Cart\Test;

use Dilab\Cart\Cart;
use Dilab\Cart\Enum\VariantStatus;
use Dilab\Cart\Event;
use Dilab\Cart\Registration;
use Dilab\Cart\Test\Factory\DonationFactory;
use Dilab\Cart\Test\Factory\EntitlementFactory;
use Dilab\Cart\Test\Factory\EventFactory;
use Dilab\Cart\Test\Factory\FormDataFactory;
use PHPUnit\Framework\TestCase;

class RegistrationTest extends TestCase
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

        $cart->addTicket($event->getCategoryById(1), 1);

        $this->registration = new Registration($cart->getParticipants());
    }

    public function testRenderParticipant()
    {
        $trackId = 0;

        $expected = [
            'form' => FormDataFactory::emptyData(),
            'entitlements' => EntitlementFactory::entitlements(),
            'fundraises' => DonationFactory::emptyDonation(),
            'customFields' => []
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
            'donations' => []
        ];

        $this->registration->renderParticipant($trackId);

        $this->assertFalse($this->registration->fillParticipant($trackId, $data));

        $this->assertFalse($this->registration->isCompleted($trackId));

        $data = [
            'form' => FormDataFactory::correctData(),
            'entitlements' => EntitlementFactory::postData(),
            'donations' => DonationFactory::postData()

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
            'donations' => DonationFactory::postData()
        ];

        $this->assertFalse($this->registration->isCompleted($trackId));

        $this->registration->renderParticipant($trackId);

        $this->assertTrue($this->registration->fillParticipant($trackId, $data));

        $this->assertTrue($this->registration->isCompleted($trackId));

        $this->assertEquals(1, $this->registration->redirectTo());
        // next participant
        $trackId = 1;

        $data = [
            'form' => FormDataFactory::correctData(),
            'entitlements' => [],
            'donations' => []
        ];

        $this->assertFalse($this->registration->isCompleted($trackId));

        $this->registration->renderParticipant($trackId);

        $this->assertTrue($this->registration->fillParticipant($trackId, $data));

        $this->assertTrue($this->registration->isCompleted($trackId));

        $this->assertEquals(Registration::SUMMARY, $this->registration->redirectTo());
    }

    public function testFillForm()
    {
        $trackId = 1;
        $participant = $this->registration->getParticipantByTrackId($trackId);
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
        $this->assertEquals($expected, $this->registration->renderForm($participant));

        $trackId = 0;
        $participant = $this->registration->getParticipantByTrackId($trackId);
        $expected = FormDataFactory::emptyData();
        $result = $this->registration->renderForm($participant);
        $this->assertEquals($expected, $result);

        $trackId = 1;
        $participant = $this->registration->getParticipantByTrackId($trackId);
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
        $this->registration->renderForm($participant);
        $this->assertTrue($this->registration->fillForm($participant, $data));
        $result = $this->registration->renderForm($participant);
        $this->assertEquals($data, $result);
    }

    public function testFillEntitlement()
    {
        $trackId = 0;
        $participant = $this->registration->getParticipantByTrackId($trackId);
        $expected = EntitlementFactory::entitlements();
        $result = $this->registration->renderEntitlements($participant);
        $this->assertEquals($expected, $result);

        $requestData = [];
        $result = $this->registration->fillEntitlements($participant, $requestData);
        $this->assertFalse($result);
        $this->assertArrayHasKey('entitlements', $this->registration->getErrors($trackId));

        $requestData = [
            1 => ''
        ];
        $result = $this->registration->fillEntitlements($participant, $requestData);
        $this->assertFalse($result);
        $this->assertArrayHasKey('entitlements', $this->registration->getErrors($trackId));

        $requestData = [
            1 => 1,
            2 => 3
        ];
        $result = $this->registration->fillEntitlements($participant, $requestData);
        $this->assertTrue($result);
    }

    public function test_fill_entitlement_without_available_variant_should_not_valid()
    {
        // arrange data
        $data = [
            'id' => 1,
            'name' => 'Changsha Marathon 2018',
            'currency' => 'SGD',
            'service_fee' => ['percentage'=>0, 'fixed'=>0],
            'categories' => [
                [
                    'id' => 1,
                    'name' => 'Men Open, 10km',
                    'price' => 1000,
                    'participants' => [
                        [
                            'id' => 1,
                            'name' => 'Runner 1',
                            'rules' => [
                                'age' => '>=18',
                                'gender' => 'male',
                                'nric' => '',
                            ],
                            'fundraises' => [
                            ],
                            'fields' => [
                                'email',
                                'dob',
                                'first_name',
                                'last_name',
                                'gender',
                                'nationality',
                            ],
                            'entitlements'=> [
                                [
                                    'id'=> 1,
                                    'name'=> 'shorts',
                                    'description' => 'Running Singlet',
                                    'image_chart' => '',
                                    'image_large' => '',
                                    'image_thumb' => '',
                                    'variants'=> [
                                        [
                                            'id'=>1,
                                            'status'=>VariantStatus::INACTIVE,
                                            'name'=>'size:s',
                                            'stock'=>100,
                                        ],
                                        [
                                            'id'=>2,
                                            'status'=>VariantStatus::ACTIVE,
                                            'name'=>'size:m',
                                            'stock'=>0,
                                        ],
                                        [
                                            'id'=>3,
                                            'status'=>VariantStatus::ACTIVE,
                                            'name'=>'size:l',
                                            'stock'=>0,
                                        ]
                                    ]
                                ]
                            ],
                        ],
                    ]
                ],
            ],
            'products'=> [
            ]
        ];

        $event = Event::init($data);

        $cart = new Cart('xuding@spacebib.com', $event);

        $cart->addTicket($event->getCategoryById(1), 1);

        $this->registration = new Registration($cart->getParticipants());

        $trackId = 0;

        $participant = $this->registration->getParticipantByTrackId($trackId);

        $this->registration->renderEntitlements($participant);

        $requestData = [];

        $result = $this->registration->fillEntitlements($participant, $requestData);

        $this->assertTrue($result);

        $requestData = [
            1 => null
        ];

        $result = $this->registration->fillEntitlements($participant, $requestData);

        $this->assertTrue($result);
    }
}
