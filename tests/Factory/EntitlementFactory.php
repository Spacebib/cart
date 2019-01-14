<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/26
 * Time: 下午4:55
 */

namespace Dilab\Cart\Test\Factory;

use Dilab\Cart\Entitlements\Entitlement;
use Dilab\Cart\Entitlements\Variant;
use Dilab\Cart\Enum\VariantStatus;

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
            'visible' => true,
            'variants'=> [
                [
                    'id'=>1,
                    'status'=>VariantStatus::ACTIVE,
                    'stock'=>10,
                    'name'=>'size:s'
                ],
                [
                    'id'=>2,
                    'status'=>VariantStatus::ACTIVE,
                    'stock'=>10,
                    'name'=>'size:m'
                ],
                [
                    'id'=>3,
                    'status'=>VariantStatus::ACTIVE,
                    'stock'=>0,
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
            'visible' => true,
            'variants'=> [
                [
                    'id'=>3,
                    'status'=>VariantStatus::ACTIVE,
                    'stock'=>10,
                    'name'=>'size:s'
                ],
                [
                    'id'=>4,
                    'status'=>VariantStatus::ACTIVE,
                    'stock'=>10,
                    'name'=>'size:m'
                ],
                [
                    'id'=>5,
                    'status'=>VariantStatus::ACTIVE,
                    'stock'=>0,
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
                $data['visible'],
                array_map(function ($v) {
                    return new Variant($v['id'], $v['name'], $v['status'], $v['stock']);
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