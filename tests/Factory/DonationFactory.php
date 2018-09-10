<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/27
 * Time: 上午10:43
 */

namespace Dilab\Cart\Test\Factory;

use Dilab\Cart\Donation\Donation;
use Dilab\Cart\Donation\Form;

class DonationFactory
{
    public static $data = [
        'id' => 1,
        'min' => 1000,
        'max' => 10000,
        'name' => 'donation',
        'required' => true,
        'fields' => [
            'fundraise_amount' => '',
            'fundraise_remark' => ''
        ]
    ];

    public static function emptyDonation()
    {
        $donation[] = new Donation(
            self::$data['id'],
            self::$data['name'],
            new Form(
                self::$data['fields'],
                [
                    'min' => self::$data['min'],
                    'max' => self::$data['max'],
                    'required' => self::$data['required'],
                ]
            ),
            'SGD'
        );

        return $donation;
    }

    public static function postData($id = 1)
    {
        return [
            $id =>  [
                'fundraise_amount' => '20',
                'fundraise_remark' => '',
            ],
            $id =>  [
                'fundraise_amount' => '20',
                'fundraise_remark' => '',
            ],
            $id =>  [
                'fundraise_amount' => '20',
                'fundraise_remark' => '',
            ],
            $id =>  [
                'fundraise_amount' => '20',
                'fundraise_remark' => '',
            ],
        ];
    }
}
