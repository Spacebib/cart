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
        $this->form = new Form(['email', 'first_name']);
    }

    public function fillProvider()
    {
        return [
            [
                [
                    'email' => 'xuding@spacebib.com',
                    'first_name' => 'xu'
                ],
                true
            ],
            [
                [
                    'email' => '',
                    'first_name' => 'xu'
                ],
                false,
                ['email']
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

    public function testSerialization()
    {
        $this->assertEquals(
            $this->form,
            Form::deserialize($this->form->serialize())
        );
    }
}
