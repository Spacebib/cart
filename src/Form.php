<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 14/3/18
 * Time: 9:56 AM
 */

namespace Dilab\Cart;


class Form
{
    private $fields;

    private $data;

    /**
     * Form constructor.
     * @param $fields
     */
    public function __construct($fields)
    {
        $this->fields = $fields;
    }

    public function fill($fillData)
    {
        $data = array_filter($fillData, function ($key) {

            return in_array($key, $this->fields);

        }, ARRAY_FILTER_USE_KEY);

        $this->data = $data;

        return $this;
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

}