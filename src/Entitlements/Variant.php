<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/26
 * Time: 上午11:02
 */

namespace Dilab\Cart\Entitlements;

class Variant
{
    private $id;

    private $status;
    
    private $stock;

    private $name;

    private $selected = false;

    public function __construct(
        $id,
        $name,
        $status,
        $stock,
        $selected = false
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->status = $status;
        $this->stock = $stock;
        $this->selected = $selected;
    }

    public function hasStock()
    {
        return $this->stock > 1;
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

    /**
     * @return mixed
     */
    public function getStock()
    {
        return $this->stock;
    }
}
