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

}