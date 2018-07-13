<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/21
 * Time: 上午10:57
 */

namespace Dilab\Cart\Rules;

class RuleGender implements Rule
{
    use TruncateError;

    private $allowedGender;

    private $errors = [];

    public function __construct($allowedGender)
    {
        $this->allowedGender = $allowedGender;
    }

    public function valid($data)
    {
        $this->truncateError();

        if (!isset($data['gender'])) {
            return true;
        }

        $genderRule = new GenderRule($this->allowedGender);

        if ($genderRule->match($data['gender'])) {
            $this->errors = [];
            return true;
        }

        $this->errors = ['gender' => $genderRule->describe()];

        return false;
    }

    public function errors()
    {
        return $this->errors;
    }
}
