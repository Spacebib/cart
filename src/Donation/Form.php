<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/27
 * Time: 上午10:10
 */

namespace Dilab\Cart\Donation;

use Dilab\Cart\Money;

class Form
{
    use Validator;

    private $fields;

    private $rules;

    private $errors = [];

    public function __construct(array $fields, $rules)
    {
        $this->fields = $fields;
        $this->rules = $rules;
    }

    public function fill($data, $donationId): bool
    {
        $this->fields = $data;

        return $this->valid($data, $donationId);
    }

    public function getAmount()
    {
        $value = $this->fields[Fields::FUNDRAISE_AMOUNT];

        return is_numeric($value) ? $value : 0;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->fields;
    }
}
