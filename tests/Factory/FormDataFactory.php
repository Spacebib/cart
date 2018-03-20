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
            'dob' => ['day'=>1, 'month'=>2, 'year'=>1995],
            'first_name' => 'xu',
            'last_name' => 'ding',
            'gender' => 'male',
            'nationality' => 'China',
            'mobile_number' => ['code'=>'china', 'number'=> '1343'],
            'address' => ['address'=>'d', 'city'=>'cs', 'zip'=>'11'],
            'blood_type' => 'O+',
            'is_med_cond' => 0,
            'med_cond' => '',
            'allergy' => '',
            'emy_contact_name' => ['code'=>'china', 'number'=> '1343'],
            'emy_relationship' => 'father',
            'emy_contact_no' => ['code'=>'china', 'number'=> '1343'],
            'nric' => 'fd',
            'name_on_bib' => 'kaiwei',
            'kin_contact_name' => '',
            'kin_contact_no' => ['code'=>'', 'number'=> '']
        ];
        return $data;
    }
}