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
                                'age' => '>=18',
                                'gender' => 'male'
                            ],
                            'fields' => [
                                'email',
                                'dob',
                                'first_name',
                                'last_name',
                                'gender',
                                'nationality',
                                'mobile_number',
                                'address_standard',
                                'blood_type',
                                'is_med_cond',
                                'med_cond',
                                'allergy',
                                'emy_contact_name',
                                'emy_relationship',
                                'emy_contact_no',
                                'nric',
                                'name_on_bib',
                                'kin_contact_name',
                                'kin_contact_no'
                            ]
                        ],
                        [
                            'id' => 2,
                            'name' => 'Runner 2',
                            'rules' => [
                                'age' => '>=18',
                                'gender' => 'male'
                            ],
                            'fields' => [
                                'email',
                                'dob',
                                'first_name',
                                'last_name',
                                'nationality',
                                'name_on_bib',
                                'gender'
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
                                'age' => '>=18',
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
                                'age' => '>=18',
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