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

    private $category;

    private $name;

    private $rules;

    /**
     * Participant constructor.
     * @param $id
     * @param $trackId
     * @param $category
     * @param $name
     * @param $rules
     */
    public function __construct($id, $trackId, $category, $name, $rules)
    {
        $this->id = $id;
        $this->trackId = $trackId;
        $this->category = $category;
        $this->name = $name;
        $this->rules = $rules;
    }



}