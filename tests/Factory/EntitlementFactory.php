<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/26
 * Time: 下午4:55
 */

namespace Dilab\Cart\Test\Factory;

use Dilab\Cart\Entitlement;
use Dilab\Cart\Variant;

class EntitlementFactory
{
    public static $data = [
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
                    'status'=>1,
                    'name'=>'size:s'
                ],
                [
                    'id'=>2,
                    'status'=>1,
                    'name'=>'size:m'
                ],
                [
                    'id'=>3,
                    'status'=>1,
                    'name'=>'size:l'
                ]
            ]
        ],
        [
            'id'=> 2,
            'name'=> 't-shirt',
            'description' => 'Running Singlet',
            'image_chart' => '',
            'image_large' => '',
            'image_thumb' => '',
            'variants'=> [
                [
                    'id'=>3,
                    'status'=>1,
                    'name'=>'size:s'
                ],
                [
                    'id'=>4,
                    'status'=>1,
                    'name'=>'size:m'
                ],
                [
                    'id'=>5,
                    'status'=>1,
                    'name'=>'size:l'
                ]
            ]
        ]

    ];

    public static function entitlements()
    {
        $entitlements = array_map(function ($data) {
            return new Entitlement(
                $data['id'],
                $data['name'],
                $data['description'],
                $data['image_chart'],
                $data['image_large'],
                $data['image_thumb'],
                array_map(function ($v) {
                    return new Variant($v['id'], $v['name'], $v['status']);
                }, $data['variants'])
            );
        }, self::$data);

        return $entitlements;
    }

    public static function postData()
    {
        return [
            1 => 1,
            2 => 4
        ];

    }
}