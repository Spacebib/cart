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
    use CartHelper;

    private $fields;

    private $data;

    private $errors = [];

    private $rules;

    /**
     * Form constructor.
     * @param $fields
     * @param $rules
     */
    public function __construct($rules, $fields)
    {
        $fields = $this->transformFields($fields);
        $this->fields = $fields;
        $this->data = $fields;
        $this->rules = $rules;
    }

    /**
     * @param $fillData
     * @return boolean
     */
    public function fill($fillData)
    {
        $data = $this->readWhatIsDefined($fillData);

        $this->data = $data;

        if (!$this->valid($data)) {
            return false;
        }

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

            return in_array($key, array_keys($this->fields));

        }, ARRAY_FILTER_USE_KEY);
    }

    private function valid($data)
    {
        if (! $this->fieldsNotBeEmpty($data)) {
            return false;
        }
        return $this->validateRules($data);
    }

    private function fieldsNotBeEmpty($data)
    {
        $emptyFields = $this->getEmptyFields(array_merge($this->fields, $data));

        $notRequiredFields = $this->getNotRequiredFields($data);

        $emptyFields = array_diff($emptyFields, $notRequiredFields);

        if (!empty($emptyFields)) {
            $this->errors = array_fill_keys(
                $emptyFields,
                'Field can not be empty'
            );
        }

        return empty($emptyFields);
    }

    private function validateRules($data)
    {
        while ($rule = $this->getNextRule()) {
            if (!$this->validateRule($rule, $data)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param \Dilab\Cart\Rules\Rule $rule
     * @param $data
     * @return bool
     */
    private function validateRule($rule, $data)
    {
        if (! $rule->valid($data)) {
            $this->setErrors(array_merge($this->errors, $rule->errors()));
            return false;
        }
        return true;
    }

    private function getNextRule()
    {
        return array_pop($this->rules);
    }

    private function transformFields($fields)
    {
        $newFields = [];
        foreach ($fields as &$field) {
            switch ($field) {
                case 'dob':
                    $newFields['dob'] = ['day'=>'', 'month'=>'', 'year'=>''];
                    break;
                case 'mobile_number':
                case 'emy_contact_no':
                case 'kin_contact_no':
                    $newFields[$field] = ['code'=>'', 'number'=>''];
                    break;
                case 'address_standard':
                    $newFields['address'] = [
                        'address'=>'',
                        'city'=>'',
                        'state'=>'',
                        'zip'=>'',
                    ];
                    break;
                case 'address_sg_standard':
                    $newFields['address'] = [
                        'block' => '',
                        'unit_prefix' => '',
                        'unit_suffix' => '',
                        'street' => '',
                        'building' => '',
                        'postal_code' => '',
                    ];
                    break;
                default:
                    $newFields[$field] = '';
                    break;
            }
        }
        return $newFields;
    }

    private function getNotRequiredFields($data)
    {
        /**
         *  medical condition when it is false
         *  address -> state
         *  age > 18: kin_contact_name, kin_contact_no
         */
        $notRequiredFields = ['address.state'];
        if (isset($data['is_med_cond']) && $data['is_med_cond']==0) {
            $notRequiredFields = array_merge($notRequiredFields,
                ['med_cond', 'allergy']);
        }

        if (isset($data['dob']) && self::is18($data['dob'])) {
            $notRequiredFields = array_merge($notRequiredFields,
                ['kin_contact_name', 'kin_contact_no.number', 'kin_contact_no.code']);
        }
        return $notRequiredFields;
    }

    private function getEmptyFields($data, $prefix='')
    {
        $emptyFields = [];
        foreach ($data as $field=>$val) {
            if (is_array($val)) {
                $emptyFields = array_merge(
                    $emptyFields,
                    $this->getEmptyFields($val, $field.'.')
                );
            } elseif (trim($val) == '') {
                $emptyFields[] = $prefix.$field;
            }
        }
        return $emptyFields;
    }
}