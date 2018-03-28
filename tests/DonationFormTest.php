<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/27
 * Time: 上午11:59
 */

namespace Dilab\Cart\Test;

use Dilab\Cart\Donation\Form;

class DonationFormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Form
     */
    public $form;

    public function setUp()
    {
        parent::setUp();
        $this->form = new Form(
            ['fundraise_amount', 'fundraise_remark'],
            [
                'min' => 10,
                'max' => 100,
                'required' => true
            ]
        );
    }

    public function fillProvider()
    {
        return [
            [
                [
                    'fundraise_amount' => '',
                    'fundraise_remark' => '',
                ],
                false
            ],
            [
                [
                    'fundraise_amount' => 'fdfd',
                    'fundraise_remark' => '',
                ],
                false
            ],
            [
                [
                    'fundraise_amount' => '0.1',
                    'fundraise_remark' => '',
                ],
                false
            ],
            [
                [
                    'fundraise_amount' => '20',
                    'fundraise_remark' => base64_encode(random_bytes(500)),
                ],
                false
            ],
            [
                [
                    'fundraise_amount' => '20',
                    'fundraise_remark' => '',
                ],
                true
            ],
        ];

    }

    /**
     * @dataProvider fillProvider
     * @param $data
     * @param $expected
     */
    public function testFill($data, $expected)
    {
        $result = $this->form->fill($data);

        $this->assertEquals($expected, $result);

        if ($expected) {
            $this->assertSame($data, $this->form->getData());
        }
    }
}
