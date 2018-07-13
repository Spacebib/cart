<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/22
 * Time: 下午2:26
 */

namespace Dilab\Cart\Rules;

class RuleLength implements Rule
{
    use TruncateError;

    private $errors = [];

    public function valid($data)
    {
        $this->truncateError();

        return $this->validateLength($data);
    }

    public function errors()
    {
        return $this->errors;
    }

    private function validateLength(array $data): bool
    {
        $flag = true;

        foreach ($data as $field => $value) {
            switch ($field) {
                case 'email':
                case 'email_confirmation':
                case 'first_name':
                case 'last_name':
                case 'emy_contract_name':
                    if (strlen($value) > 100) {
                        $this->setLengthErrors($field, 100);
                        $flag = false;
                    }
                    break;
                case 'name_on_bib':
                case 'nric':
                    if (strlen($value) > 20) {
                        $this->setLengthErrors($field, 20);
                        $flag = false;
                    }
                    break;
                default:
                    break;
            }
        }

        return $flag;
    }

    private function setLengthErrors($field, $maxLength)
    {
        $this->errors = array_merge(
            $this->errors(),
            [
                $field => sprintf('%s cannot be longer than %s characters.', $field, $maxLength)
            ]
        );
    }
}
