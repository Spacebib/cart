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
}