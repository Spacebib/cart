<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/21
 * Time: 上午9:37
 */

namespace Dilab\Cart\Rules;


interface Rule
{
    public function valid($data);

    public function errors();
}