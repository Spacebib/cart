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

    private $errors;

    /**
     * CustomFields constructor.
     *
     * @param $fields
     */
    public function __construct(array $fields)
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
        return array_filter(
            $data,
            function ($key) {
                return in_array($key, array_keys($this->fields));
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    private function valid($data)
    {
        $flag = true;

        foreach ($this->fields as $key => $field) {
            $value = array_get($data, $key);
            $this->fields[$key]['value'] = $value;
            $this->fields[$key]['valid'] = true;
            $this->fields[$key]['error'] = null;

            if (data_get($field, 'validation.required.enabled') && ('' === $value || is_null($value))) {
                $this->errors[$key] = data_get($field, 'validation.required.error');
                $flag = false;
            } elseif (data_get($field, 'validation.regex.enabled')) {
                if (in_array(data_get($field, 'type'), ['checkbox'])) {
                    continue;
                }
                if (1 !== preg_match("/".data_get($field, 'validation.regex.pattern')."/u", $value)) {
                    $this->errors[$key] = data_get($field, 'validation.regex.error');
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

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param mixed $errors
     */
    public function setErrors($errors): void
    {
        $this->errors = $errors;
    }
}
