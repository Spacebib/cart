<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/27
 * Time: 上午10:10
 */

namespace Dilab\Cart\Donation;

class Form
{
    private $fields;

    private $rules;

    private $errors = [];

    public function __construct(array $fields, $rules)
    {
        $this->fields = $fields;
        $this->rules = $rules;
    }

    public function fill($data, $donationId)
    {
        $this->fields = $data;

        if ($this->valid($data, $donationId)) {
            return true;
        }
        return false;
    }

    private function valid(array $data, $donationId)
    {
        if ($this->isEmpty($data)) {
            $this->errors['fundraise_amount'][$donationId] = 'Amount can not be empty';
            return false;
        }

        if (! is_numeric($data['fundraise_amount'])) {
            $this->errors['fundraise_amount'][$donationId] = 'Amount should be a number';
            return false;
        }

        if ($data['fundraise_amount'] < $this->rules['min']) {
            $this->errors['fundraise_amount'][$donationId] = sprintf('Minimum %s', $this->rules['min']);
            return false;
        }

        if ($data['fundraise_amount'] > $this->rules['max']) {
            $this->errors['fundraise_amount'][$donationId] = 'Amount is too large';
            return false;
        }

        if (strlen($data['fundraise_remark']) > 250) {
            $this->errors['fundraise_remark'][$donationId] = 'remark is too long';
            return false;
        }
        return true;
    }

    private function isEmpty(array $data)
    {
        if (! (isset($data['fundraise_amount']) || isset($data['fundraise_remark']))) {
            return true;
        }
        if ($this->rules['required'] && !$data['fundraise_amount']) {
            return true;
        }
        return false;
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
