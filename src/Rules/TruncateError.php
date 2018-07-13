<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/7/13
 * Time: 下午2:41
 */

namespace Dilab\Cart\Rules;

trait TruncateError
{
    protected function truncateError()
    {
        $this->errors = [];
    }
}
