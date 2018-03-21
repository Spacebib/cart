<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/21
 * Time: 上午10:39
 */

namespace Dilab\Cart\Rules;


class RuleException extends \RuntimeException
{
    public static function invalidAgeRuleString($ageAllowedString)
    {
        return new static(sprintf('Invalid age rule string %s', $ageAllowedString));
    }

    public static function invalidGenderRuleString($genderAllowedString)
    {
        return new static(sprintf('Invalid gender rule string %s', $genderAllowedString));
    }

    public static function invalidDescription($msg)
    {
        return new static($msg);

    }

}