<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/21
 * Time: 上午10:53
 */

namespace Dilab\Cart\Rules;


class GenderRule
{
    const MALE = 'male';

    const FEMALE = 'female';

    const ALL = 'all';

    private $allowedGender;

    public function __construct($allowedGender)
    {
        $this->allowedGender = $allowedGender;

        $this->parseGender();
    }

    private function parseGender()
    {
        if (!in_array($this->allowedGender, [self::MALE, self::FEMALE, self::ALL])) {
            throw RuleException::invalidGenderRuleString($this->allowedGender);
        };
    }

    public function match($gender)
    {
        if (! in_array($gender, [self::MALE, self::FEMALE])) {
            return false;
        }

        if ($this->allowedGender == self::ALL) {
            return true;
        }

        return ($this->allowedGender == $gender);
    }

    public function describe()
    {
        $options = self::rules();
        return $options[$this->allowedGender];
    }

    public static function rules()
    {
        return [
            self::ALL => ('Any Gender'),
            self::MALE => ('Male Only'),
            self::FEMALE => ('Female Only'),
        ];
    }
}