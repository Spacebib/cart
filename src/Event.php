<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 13/3/18
 * Time: 5:55 PM
 */

namespace Dilab\Cart;


class Event
{
    private $id;

    private $name;

    private $categories;

    /**
     * Event constructor.
     * @param $id
     * @param $name
     * @param array $categories
     */
    public function __construct($id, $name, array $categories)
    {
        $this->id = $id;
        $this->name = $name;
        $this->categories = $categories;
    }

    public function getParticipants()
    {

    }

    public function getCategoryById($categoryId)
    {

    }
}