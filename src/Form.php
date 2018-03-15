<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 14/3/18
 * Time: 9:56 AM
 */

namespace Dilab\Cart;


class Form implements Serializable
{
    private $fields;

    private $data;

    private $errors = [];

    /**
     * Form constructor.
     * @param $fields
     */
    public function __construct($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @param $fillData
     * @return boolean
     */
    public function fill($fillData)
    {
        $data = $this->readWhatIsDefined($fillData);

        if (!$this->valid($data)) {
            return false;
        }

        $this->data = $data;

        $this->errors = [];

        return true;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @param array $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return mixed
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    private function readWhatIsDefined($fillData)
    {
        return array_filter($fillData, function ($key) {

            return in_array($key, $this->fields);

        }, ARRAY_FILTER_USE_KEY);
    }

    public function serialize()
    {
        return [
            'fields' => $this->fields,
            'data' => $this->data,
            'errors' => $this->errors
        ];
    }

    public static function deserialize($data)
    {
        $obj = new Form($data['fields']);
        $obj->setData($data['data']);
        $obj->setErrors($data['errors']);
        return $obj;
    }

    private function valid($data)
    {
        return $this->fieldsNotBeEmpty($data);
    }

    private function fieldsNotBeEmpty($data)
    {
        $emptyFields = array_filter($data, function ($val) {
            return empty($val);
        });

        if (!empty($emptyFields)) {
            $this->errors = array_fill_keys(
                array_keys($emptyFields),
                'Field can not be empty'
            );
        }

        return empty($emptyFields);
    }
}