<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/5/29
 * Time: 上午11:11
 */

namespace Dilab\Cart;

class CustomFields
{
    private $fields;

    /**
     * CustomFields constructor.
     * @param $fields
     */
    public function __construct($fields)
    {
        $fields = $this->transformFields($fields);

        $this->fields = $fields;
    }

    public function fill($data)
    {
        $data = $this->readWhatIsDefined($data);

        return $this->valid($data);
    }

    private function transformFields($fields)
    {
        $newFields = [];

        foreach ($fields as $field) {
            $newFields[$field['key']] = $field;
        }

        return $newFields;
    }

    private function readWhatIsDefined($data)
    {
        return array_filter($data, function ($key) {
            return in_array($key, array_keys($this->fields));
        }, ARRAY_FILTER_USE_KEY);
    }

    private function valid($data)
    {
        $flag = true;

        foreach ($data as $key => $value) {
            $this->fields[$key]['value'] = $value;
            $this->fields[$key]['valid'] = true;

            if ($this->fields[$key]['validation']['required']['enabled'] && ! $value) {
                $this->fields[$key]['valid'] = false;
                $this->fields[$key]['error'] = $this->fields[$key]['validation']['required']['error'];
                $flag = false;
            } elseif ($this->fields[$key]['validation']['regex']['enabled']) {
                if (1 !== preg_match("/".$this->fields[$key]['validation']['regex']['pattern']."/", $value)) {
                    $this->fields[$key]['valid'] = false;
                    $this->fields[$key]['error'] = $this->fields[$key]['validation']['regex']['error'];
                    $flag = false;
                }
            }
        }

        return $flag;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }
}
