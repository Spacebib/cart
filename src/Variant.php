<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/26
 * Time: 上午11:02
 */

namespace Dilab\Cart;

class Variant
{
    private $id;

    private $status;

    private $name;

    private $selected = false;

    public function __construct($id, $name, $status, $selected = false)
    {
        $this->id = $id;
        $this->name = $name;
        $this->status = $status;
        $this->selected = $selected;
    }

    /**
     * @return mixed
     */
    public function getSelected()
    {
        return $this->selected;
    }

    /**
     * @param mixed $selected
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;
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
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }
}
