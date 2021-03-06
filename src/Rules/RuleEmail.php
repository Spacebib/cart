<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/21
 * Time: 下午4:43
 */

namespace Dilab\Cart\Rules;

class RuleEmail implements Rule
{
    use TruncateError;

    private $errors = [];

    public function valid($data)
    {
        $this->truncateError();

        if (!isset($data['email'])) {
            return true;
        }

        if (! $this->validateEmailFormat($data['email'])) {
            $this->errors = ['email' => 'Invalid email address format'];
            return false;
        }

        if ($data['email'] !== $data['email_confirmation']) {
            $this->errors = ['email_confirmation' => 'Email addresses do not match'];
            return false;
        }

        return true;
    }

    public function errors()
    {
        return $this->errors;
    }

    private function validateEmailFormat($email)
    {
        return 1===preg_match('/^[a-z.A-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/', $email);
    }
}
