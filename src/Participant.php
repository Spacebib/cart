<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 14/3/18
 * Time: 9:38 AM
 */

namespace Dilab\Cart;


class Participant
{
    private $trackId = 0;

    private $id;

    private $name;

    private $rules;

    private $form;

    private $isTouched = false;

    private $isDirty = false;

    private $isCompleted = false;

    /**
     * Participant constructor.
     * @param $id
     * @param $name
     * @param array $rules
     * @param Form $form
     */
    public function __construct($id, $name, array $rules, Form $form)
    {
        $this->id = $id;
        $this->name = $name;
        $this->rules = $rules;
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
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }


    /**
     * @return mixed
     */
    public function getTrackId()
    {
        return $this->trackId;
    }

    /**
     * @param mixed $trackId
     */
    public function setTrackId($trackId)
    {
        $this->trackId = $trackId;
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param bool $isTouched
     */
    public function setIsTouched($isTouched)
    {
        $this->isTouched = $isTouched;
    }

    /**
     * @param bool $isDirty
     */
    public function setIsDirty($isDirty)
    {
        $this->isDirty = $isDirty;
    }

    /**
     * @param bool $isCompleted
     */
    public function setIsCompleted($isCompleted)
    {
        $this->isCompleted = $isCompleted;
    }

    /**
     * @return bool
     */
    public function isTouched()
    {
        return $this->isTouched;
    }

    /**
     * @return bool
     */
    public function isDirty()
    {
        return $this->isDirty;
    }

    /**
     * @return bool
     */
    public function isCompleted()
    {
        return $this->isCompleted;
    }

}