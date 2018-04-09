<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/20
 * Time: 下午4:23
 */

namespace Dilab\Cart\Test\Factory;

class FormDataFactory
{
    public static function correctData()
    {
        $data = [
            'email' => 'xuding@spacebib.com',
            'email_confirmation' => 'xuding@spacebib.com',
            'salutation' => 'Mr',
            'dob' => ['day'=>1, 'month'=>2, 'year'=>1995],
            'first_name' => 'xu',
            'last_name' => 'ding',
            'gender' => 'male',
            'nationality' => 'Chinese',
            'mobile_number' => ['code'=>'china', 'number'=> '1343'],
            'address_type' => 'HDB',
            'address' => ['address'=>'d', 'city'=>'cs', 'zip'=>'11'],
            'country_of_red' => 'CN',
            'blood_type' => 'O+',
            'is_med_cond' => 0,
            'med_cond' => '',
            'allergy' => '',
            'emy_contact_name' => ['code'=>'china', 'number'=> '1343'],
            'emy_relationship' => 'father',
            'emy_contact_no' => ['code'=>'china', 'number'=> '1343'],
            'id_type' => 'NRIC',
            'nric' => 'fd',
            'name_on_bib' => 'kaiwei',
            'kin_contact_name' => '',
            'kin_contact_no' => ['code'=>'', 'number'=> '']
        ];
        return $data;
    }

    public static function emptyData()
    {
        $data = [
            'email' => '',
            'email_confirmation' => '',
            'salutation' => '',
            'dob' => ['day'=>'', 'month'=>'', 'year'=>''],
            'first_name' => '',
            'last_name' => '',
            'gender' => '',
            'nationality' => '',
            'mobile_number' => ['code'=>'', 'number'=>''],
            'address_type' => '',
            'address' => [
                'address'=>'',
                'city'=>'',
                'state'=>'',
                'zip'=>'',
            ],
            'country_of_red' => '',
            'blood_type' => '',
            'is_med_cond' => '',
            'med_cond' => '',
            'allergy' => '',
            'emy_contact_name' => '',
            'emy_relationship' => '',
            'emy_contact_no' => ['code'=>'', 'number'=>''],
            'id_type' => '',
            'nric' => '',
            'name_on_bib' => '',
            'kin_contact_name' => '',
            'kin_contact_no' => ['code'=>'', 'number'=>'']
        ];
        return $data;
    }
}