<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 14/3/18
 * Time: 10:42 AM
 */

namespace Dilab\Cart;


trait CartHelper
{
    public static function getWithException($data, $path)
    {
        if (!isset($data[$path])) {
            throw new \LogicException(
                sprintf('Data %s is not set in %s', $path, json_encode($data, true))
            );
        }

        return $data[$path];
    }

    public static function getAge($dob)
    {
        list ('year'=>$year, 'month'=>$month, 'year'=>$day) = $dob;
        $age = date('Y') - $year + 1;

        if (date('n') < $month) {
            $age -= 1;
        } elseif (date('j') < $day) {
            $age -= 1;
        }

        return $age;
    }

    public static function is18($dob)
    {
        $age = self::getAge($dob);
        return $age >= 18;
    }
}