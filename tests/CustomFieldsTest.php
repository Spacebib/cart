<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/5/29
 * Time: 下午12:03
 */

namespace Dilab\Cart\Test;

use Dilab\Cart\CustomFields;
use PHPUnit\Framework\TestCase;

class CustomFieldsTest extends TestCase
{
    /**
     * @var CustomFields
     */
    private $form;

    public function setUp()
    {
        parent::setUp();

        $fields = array (
            0 =>
                array (
                    'label' => 'Untitled',
                    'help_text' => '',
                    'tooltip' => '',
                    'tooltip_title' => '',
                    'size' => 'large',
                    'validation' =>
                        array (
                            'required' =>
                                array (
                                    'enabled' => true,
                                    'error' => 'Required.',
                                ),
                            'regex' =>
                                array (
                                    'enabled' => true,
                                    'error' => 'Invalid format',
                                    'pattern' => '\d+',
                                ),
                        ),
                    'values' =>
                        array (
                            0 => 'First Choice',
                            1 => 'Second Choice',
                        ),
                    'key' => 'untitled',
                    'type' => 'text',
                ),
            1 =>
                array (
                    'label' => 'Untitled',
                    'help_text' => '',
                    'tooltip' => '',
                    'tooltip_title' => '',
                    'size' => 'large',
                    'validation' =>
                        array (
                            'required' =>
                                array (
                                    'enabled' => true,
                                    'error' => 'Required.',
                                ),
                            'regex' =>
                                array (
                                    'enabled' => false,
                                    'error' => 'Invalid format',
                                    'pattern' => '',
                                ),
                        ),
                    'values' =>
                        array (
                            0 => 'First Choice',
                            1 => 'Second Choice',
                        ),
                    'key' => 'untitled_1',
                    'type' => 'textarea',
                ),
            2 =>
                array (
                    'label' => 'Untitled',
                    'help_text' => '',
                    'tooltip' => '',
                    'tooltip_title' => '',
                    'size' => 'large',
                    'validation' =>
                        array (
                            'required' =>
                                array (
                                    'enabled' => false,
                                    'error' => 'Required.',
                                ),
                            'regex' =>
                                array (
                                    'enabled' => false,
                                    'error' => 'Invalid format',
                                    'pattern' => '',
                                ),
                        ),
                    'values' =>
                        array (
                            0 => 'First Choice',
                            1 => 'Second Choice',
                        ),
                    'key' => 'untitled_2',
                    'type' => 'checkbox',
                ),
        );
        
        $this->form = new CustomFields($fields);
    }

    public function test_fill()
    {
        foreach ($this->form->getFields() as $field) {
            $this->assertArrayNotHasKey('value', $field);
        }

        $data = ['untitled' => 1, 'untitled_1' => 2, 'untitled_2' => 'First Choice'];

        $this->assertTrue($this->form->fill($data));

        foreach ($this->form->getFields() as $key => $field) {
            $this->assertArrayHasKey('value', $field);
        }
    }

    public function test_fill_with_invalid_key()
    {
        $data = ['untitled' => 1, 'unti' => 'First Choice'];

        $this->form->fill($data);

        $this->assertArrayNotHasKey('unti', $this->form->getFields());
    }

    public function test_fill_with_required()
    {
        $data = ['untitled' => 1, 'untitled_1' => '', 'untitled_2' => 'First Choice'];

        $this->assertFalse($this->form->fill($data));

        $this->assertArrayHasKey('untitled_1', $this->form->getErrors());
    }

    public function test_fill_with_regex()
    {
        $data = ['untitled' => 'fd', 'untitled_1' => '1', 'untitled_2' => 'First Choice'];

        $this->assertFalse($this->form->fill($data));

        $this->assertArrayHasKey('untitled', $this->form->getErrors());
        $this->assertEquals('Invalid format', $this->form->getErrors()['untitled']);
    }

    public function test_fill_with_regex_in_Vietnamese()
    {
        $this->formWithVietnameseRegex();

        $data = ['untitled' => 'ảựăậ ảự'];

        $this->assertTrue($this->form->fill($data));
    }

    public function test_fill_with_required_with_zero_value()
    {
        $data = ['untitled' => 1, 'untitled_1' => '0', 'untitled_2' => 'First Choice'];

        $this->assertTrue($this->form->fill($data));
        $this->assertNull($this->form->getErrors());
    }

    private function formWithVietnameseRegex()
    {
        $fields = array (
            0 =>
                array (
                    'label' => 'Untitled',
                    'help_text' => '',
                    'tooltip' => '',
                    'tooltip_title' => '',
                    'size' => 'large',
                    'validation' =>
                        array (
                            'required' =>
                                array (
                                    'enabled' => true,
                                    'error' => 'Required.',
                                ),
                            'regex' =>
                                array (
                                    'enabled' => true,
                                    'error' => 'Invalid format',
                                    'pattern' => '^.{1,7}$',
                                ),
                        ),
                    'values' =>
                        array (
                            0 => 'First Choice',
                            1 => 'Second Choice',
                        ),
                    'key' => 'untitled',
                    'type' => 'text',
                ),
        );

        $this->form = new CustomFields($fields);
    }
}
