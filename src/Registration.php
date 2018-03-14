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
    public function renderFormData($trackId)
    {
        $participant = $this->getParticipantByTrackId($trackId);

        $participant->setIsTouched(true);

        $form = $participant->getForm();

        $form->fill(
            array_combine(
                $form->getFields(),
                array_fill(0, count($form->getFields()), '')
            )
        );

        return $form->getData();
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
        return $this->getParticipantByTrackId($trackId)->isDirty();
    }

    public function isTouched($trackId)
    {
        return $this->getParticipantByTrackId($trackId)->isTouched();
    }

    public function isCompleted($trackId)
    {
        return $this->getParticipantByTrackId($trackId)->isCompleted();
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