<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 14/3/18
 * Time: 10:14 AM
 */

namespace Dilab\Cart\Test\Factory;

use Dilab\Cart\Event;

class EventFactory
{
    public static function create()
    {
        $data = [
            'id' => 1,
            'name' => 'Changsha Marathon 2018',
            'currency' => 'SGD',
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
                                'age' => '>50',
                                'gender' => 'male'
                            ],
                            'fields' => [
                                'fields' => [
                                    'email',
                                    'dob',
                                    'first_name',
                                    'last_name'
                                ]
                            ]
                        ],
                        [
                            'id' => 2,
                            'name' => 'Runner 2',
                            'rules' => [
                                'age' => '>50',
                                'gender' => 'male'
                            ],
                            'fields' => [
                                'email',
                                'dob',
                                'first_name',
                                'last_name'
                            ]
                        ]
                    ]
                ],
                [
                    'id' => 2,
                    'name' => 'Men Open, 10km',
                    'price' => 50000,
                    'participants' => [
                        [
                            'id' => 1,
                            'name' => 'Runner 1',
                            'rules' => [
                                'age' => '>50',
                                'gender' => 'male'
                            ],
                            'fields' => [
                                'email',
                                'dob',
                                'first_name',
                                'last_name'
                            ]
                        ],
                        [
                            'id' => 3,
                            'name' => 'Runner 2',
                            'rules' => [
                                'age' => '>50',
                                'gender' => 'male'
                            ],
                            'fields' => [
                                'email',
                                'dob',
                                'first_name',
                                'last_name'
                            ]
                        ]
                    ]
                ],
            ]
        ];

        return Event::init($data);
    }
}