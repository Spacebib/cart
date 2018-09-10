<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/7/13
 * Time: 下午12:09
 */

namespace Dilab\Cart\Donation;

trait Validator
{
    public function valid(array $data, $donationId)
    {
        if (!array_key_exists(Fields::FUNDRAISE_AMOUNT, $data)) {
            return false;
        }

        if ($this->isEmpty($data) && !$this->rules['required']) {
            return true;
        }

        if ($this->isEmpty($data)) {
            $this->errors[Fields::FUNDRAISE_AMOUNT][$donationId] = 'Amount can not be empty';
            return false;
        }

        if (! is_numeric($data[Fields::FUNDRAISE_AMOUNT])) {
            $this->errors[Fields::FUNDRAISE_AMOUNT][$donationId] = 'Amount should be a number';
            return false;
        }

        if ($data[Fields::FUNDRAISE_AMOUNT] < $this->rules['min']/100) {
            $this->errors[Fields::FUNDRAISE_AMOUNT][$donationId] =
                sprintf('Minimum %s', $this->rules['min']/100);
            return false;
        }

        if ($data[Fields::FUNDRAISE_AMOUNT] > $this->rules['max']/100) {
            $this->errors[Fields::FUNDRAISE_AMOUNT][$donationId] = sprintf(
                'Amount is too large, maximum %s',
                $this->rules['max']/100
            );
            return false;
        }

        if (isset($data['' . Fields::FUNDRAISE_REMARK . '']) && strlen($data['' . Fields::FUNDRAISE_REMARK . '']) > 250) {
            $this->errors[Fields::FUNDRAISE_REMARK][$donationId] = 'remark is too long';
            return false;
        }

        return true;
    }

    private function isEmpty(array $data)
    {
        return $data[Fields::FUNDRAISE_AMOUNT]==='' || is_null($data[Fields::FUNDRAISE_AMOUNT]);
    }
}
