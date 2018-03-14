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
    private $trackId;

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
     * @param $trackId
     * @param $name
     * @param array $rules
     * @param Form $form
     */
    public function __construct($id, $trackId, $name, array $rules, Form $form)
    {
        $this->id = $id;
        $this->trackId = $trackId;
        $this->name = $name;
        $this->rules = $rules;
        $this->form = $form;
    }

    /**
     * @return mixed
     */
    public function getTrackId()
    {
        return $this->trackId;
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