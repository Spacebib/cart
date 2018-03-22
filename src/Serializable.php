<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 15/3/18
 * Time: 2:32 PM
 */

namespace Dilab\Cart;

use Zumba\JsonSerializer\JsonSerializer;

trait Serializable
{
    public function serialize()
    {
        $serializer = new JsonSerializer();
        return $serializer->serialize($this);
    }

    public static function deserialize($data)
    {
        $serializer = new JsonSerializer();
        return $serializer->unserialize($data);
    }
}
