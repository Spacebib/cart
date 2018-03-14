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

    public function testFill()
    {
        $this->form->fill([
            'email' => 'xuding@spacebib.com',
            'first_name' => 'xu',
            'last_name' => 'ding'
        ]);

        $expected = [
            'email' => 'xuding@spacebib.com',
            'first_name' => 'xu',
        ];

        $this->assertSame($expected, $this->form->getData());
    }
}
