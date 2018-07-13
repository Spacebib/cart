<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/21
 * Time: 上午10:34
 */

namespace Dilab\Cart\Rules;

use Dilab\Cart\Traits\CartHelper;

class RuleAge implements Rule
{
    use CartHelper;

    use TruncateError;

    private $allowedAge;

    private $errors = [];

    public function __construct($allowedAge)
    {
        $this->allowedAge = $allowedAge;
    }

    public function valid($data)
    {
        $this->truncateError();

        if (!isset($data['dob'])) {
            return true;
        }

        $age = self::getAge($data['dob']);

        $ageRule = new AgeRule($this->allowedAge);

        if ($ageRule->match($age)) {
            return true;
        }

        $this->errors = ['dob' => $ageRule->describe()];

        return false;
    }

    public function errors()
    {
        return $this->errors;
    }
}
