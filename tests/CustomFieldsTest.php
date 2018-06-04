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

        $this->assertArrayNotHasKey('unti', $this->form->getData());
    }

    public function test_fill_with_required()
    {
        $data = ['untitled' => 1, 'untitled_1' => '', 'untitled_2' => 'First Choice'];

        $this->assertFalse($this->form->fill($data));

        foreach ($this->form->getFields() as $key => $field) {
            $this->assertArrayHasKey('value', $field);

            if ($key === 'untitled_1') {
                $this->assertFalse($field['valid']);
            } else {
                $this->assertTrue($field['valid']);
            }
        }
    }

    public function test_fill_with_regex()
    {
        $data = ['untitled' => 'fd', 'untitled_1' => '1', 'untitled_2' => 'First Choice'];

        $this->assertFalse($this->form->fill($data));

        foreach ($this->form->getFields() as $key => $field) {
            $this->assertArrayHasKey('value', $field);

            if ($key === 'untitled') {
                $this->assertFalse($field['valid']);
            } else {
                $this->assertTrue($field['valid']);
            }
        }
    }
}
