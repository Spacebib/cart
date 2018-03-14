<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 13/3/18
 * Time: 5:59 PM
 */

namespace Dilab\Cart;


class Registration
{
    private $participants;

    /**
     * Registration constructor.
     * @param $participants
     */
    public function __construct(array $participants)
    {
        $this->participants = $participants;
    }

    /**
     * return a list of form fields
     */
    public function renderParticipant($trackId)
    {

    }

    /**
     * return true/false
     */
    public function fillParticipant($trackId, array $data)
    {

    }

    /**
     * return trur/false
     */
    public function isRedirect()
    {

    }

    public function isDirty($trackId)
    {

    }

    public function isTouched($trackId)
    {

    }

    public function isCompleted($trackId)
    {

    }

    public function sleep()
    {}

    public static function awake()
    {

    }
}