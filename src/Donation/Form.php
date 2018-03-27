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

    public function fill($data)
    {
        $this->fields = $data;

        if ($this->valid($data)) {
            return true;
        }
        return false;
    }

    private function valid(array $data)
    {
        if (! (isset($data['fundraise_amount_in_dollar']) || isset($data['fundraise_remark']))) {
            return false;
        }
        if ($this->rules['required'] && !$data['fundraise_amount_in_dollar']) {
            return false;
        }

        if (! is_numeric($data['fundraise_amount_in_dollar'])) {

            return false;
        }

        if ($data['fundraise_amount_in_dollar'] < $this->rules['min']) {
            return false;
        }

        if ($data['fundraise_amount_in_dollar'] > $this->rules['max']) {
            return false;
        }

        if (strlen($data['fundraise_remark']) > 250) {
            return false;
        }
        return true;
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
