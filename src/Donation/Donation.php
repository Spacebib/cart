<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/27
 * Time: 上午10:01
 */

namespace Dilab\Cart\Donation;

class Donation
{
    private $id;

    private $name;

    private $form;

    public function __construct($id, $name, $form)
    {
        $this->id = $id;
        $this->name = $name;
        $this->form = $form;
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
}
