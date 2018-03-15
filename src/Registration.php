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
        $this->guardParticipants($participants);

        $this->participants = $participants;
        $this->dataStore = $dataStore;
    }


    /**
     * return a list of form fields
     */
    public function renderParticipantForm($trackId)
    {
        $participant = $this->getParticipantByTrackId($trackId);

        $form = $participant->getForm();

        $this->fillOnFirstView($participant);

        $participant->setIsTouched(true);

        return $form->getData();
    }

    public function fillParticipantForm($trackId, array $data)
    {
        $participant = $this->getParticipantByTrackId($trackId);

        $form = $participant->getForm();

        $form->fill($data);

        $participant->setIsDirty(true);

        return $this;
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

    private function fillOnFirstView(Participant $participant)
    {
        if ($participant->isTouched()) {
            return;
        }

        $participant->getForm()->fill(
            array_combine(
                $participant->getForm()->getFields(),
                array_fill(0, count($participant->getForm()->getFields()), '')
            )
        );
    }

    private function guardParticipants(array $participants)
    {
        $trackIds = array_map(function(Participant $participant) {
            return $participant->getTrackId();
        }, $participants);

        if (count(array_unique($trackIds)) != count($participants)) {
            throw new \LogicException(sprintf(
                'Invalid track IDs: %s',
                json_encode($trackIds)
            ));
        }
    }
}