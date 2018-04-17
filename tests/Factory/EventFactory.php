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
                                'gender' => 'male',
                                'nric' => '',
                            ],
                            'fundraises' => [
                                [
                                    'id' => 1,
                                    'min' => 10,
                                    'max' => 100,
                                    'name' => 'donation',
                                    'required' => true,
                                    'fields' => [
                                        'fundraise_amount' => '',
                                        'fundraise_remark' => ''
                                    ]
                                ],
                            ],
                            'fields' => [
                                'email',
                                'dob',
                                'salutation',
                                'first_name',
                                'last_name',
                                'gender',
                                'nationality',
                                'mobile_number',
                                'address_type',
                                'address',
                                'country_of_red',
                                'blood_type',
                                'is_med_cond',
                                'med_cond',
                                'allergy',
                                'emy_contact_name',
                                'emy_relationship',
                                'emy_contact_no',
                                'id_type',
                                'nric',
                                'name_on_bib',
                                'kin_contact_name',
                                'kin_contact_no',
                            ],
                            'entitlements'=> [
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
                            ],
                        ],
                        [
                            'id' => 2,
                            'name' => 'Runner 2',
                            'rules' => [
                                'age' => '>=18',
                                'gender' => 'male',
                            ],
                            'fields' => [
                                'email',
                                'dob',
                                'first_name',
                                'last_name',
                                'nationality',
                                'name_on_bib',
                                'gender'
                            ],
//                            'entitlements'=> [
//                                [
//                                    'id'=> 1,
//                                    'name'=> 'shorts',
//                                    'description' => 'Running Singlet',
//                                    'image_small' => '',
//                                    'image_large' => '',
//                                    'variants'=> [
//                                        [
//                                            'id'=>1,
//                                            'status'=>0,
//                                            'name'=>'size:s'
//                                        ],
//                                        [
//                                            'id'=>2,
//                                            'status'=>0,
//                                            'name'=>'size:m'
//                                        ],
//                                        [
//                                            'id'=>3,
//                                            'status'=>0,
//                                            'name'=>'size:l'
//                                        ]
//                                    ]
//                                ]
//                            ],
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
                            ],
                            'entitlements'=> [
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
                                            'status'=>0,
                                            'name'=>'size:s'
                                        ],
                                        [
                                            'id'=>2,
                                            'status'=>0,
                                            'name'=>'size:m'
                                        ],
                                        [
                                            'id'=>3,
                                            'status'=>0,
                                            'name'=>'size:l'
                                        ]
                                    ]
                                ]
                            ],
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
                            ],
                            'entitlements'=> [
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
                                            'status'=>0,
                                            'name'=>'size:s'
                                        ],
                                        [
                                            'id'=>2,
                                            'status'=>0,
                                            'name'=>'size:m'
                                        ],
                                        [
                                            'id'=>3,
                                            'status'=>0,
                                            'name'=>'size:l'
                                        ]
                                    ]
                                ]
                            ],
                        ]
                    ]
                ],
            ],
            'products'=> [
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
                            'name'=>'size:s',
                            'price' => 100
                        ],
                        [
                            'id'=>2,
                            'status'=>1,
                            'name'=>'size:m',
                            'price' => 100
                        ],
                        [
                            'id'=>3,
                            'status'=>1,
                            'name'=>'size:l',
                            'price' => 100
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
                            'name'=>'size:s',
                            'price' => 100
                        ],
                        [
                            'id'=>4,
                            'status'=>1,
                            'name'=>'size:m',
                            'price' => 100
                        ],
                        [
                            'id'=>5,
                            'status'=>1,
                            'name'=>'size:l',
                            'price' => 100
                        ]
                    ]
                ]
        ]
        ];

        return Event::init($data);
    }
}