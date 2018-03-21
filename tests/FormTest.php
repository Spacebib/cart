<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 14/3/18
 * Time: 12:15 PM
 */

namespace Dilab\Cart\Test;

use Dilab\Cart\Form;

class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Form
     */
    public $form;

    public function setUp()
    {
        parent::setUp();
        $this->form = new Form([], ['email', 'first_name', 'dob', 'kin_contact_no', 'kin_contact_name']);
    }

    public function fillProvider()
    {
        return [
            [
                [
                    'email' => 'xuding@spacebib.com',
                    'email_confirmation' => 'xuding@spacebib.com',
                    'first_name' => 'xu',
                    'dob'=> ['day'=>1, 'month'=>2, 'year'=>2017],
                    'kin_contact_name' => '',
                    'kin_contact_no' => ['code'=>'', 'number'=>'']
                ],
                false,
                ['kin_contact_name', 'kin_contact_no.code', 'kin_contact_no.number']
            ],

            [
                [
                    'email' => 'xuding@spacebib.com',
                    'email_confirmation' => 'xuding@spacebib.com',
                    'first_name' => 'xu',
                    'dob'=> ['day'=>1, 'month'=>2, 'year'=>1995],
                    'kin_contact_name' => '',
                    'kin_contact_no' => ['code'=>'', 'number'=>'']
                ],
                true,
            ]
        ];
    }

    /**
     * @dataProvider fillProvider
     */
    public function testFill($data, $expected, $errorFields = array())
    {
        $result = $this->form->fill($data);

        $this->assertEquals($expected, $result);

        if ($expected) {
            $this->assertSame($data, $this->form->getData());
        }

        if (!$expected) {
            foreach ($errorFields as $field) {
                $this->assertArrayHasKey($field, $this->form->getErrors());
            }
        }
    }
}
