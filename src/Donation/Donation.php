<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/27
 * Time: 上午10:01
 */

namespace Dilab\Cart\Donation;

use Dilab\Cart\Money;

class Donation
{
    private $id;

    private $name;

    private $form;

    private $currency;

    public function __construct($id, $name, Form $form, $currency)
    {
        $this->id = $id;
        $this->name = $name;
        $this->form = $form;
        $this->currency = $currency;
    }

    public function getAmount(): Money
    {
        $amount =  $this->form->getAmount();

        return Money::fromCent($this->currency, $amount*100);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }
}
