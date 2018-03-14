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

    private $dataStore;

    /**
     * Registration constructor.
     * @param array $participants
     * @param DataStore $dataStore
     */
    public function __construct(array $participants, DataStore $dataStore)
    {
        $this->participants = $participants;
        $this->dataStore = $dataStore;
    }

    /**
     * return a list of form fields
     */
    public function renderParticipant($trackId)
    {
        return $this->getParticipantByTrackId($trackId)->fo
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

    /**
     * @param $trackId
     * @return Participant
     */
    private function getParticipantByTrackId($trackId)
    {
        return $this->participants[$trackId];
    }
}