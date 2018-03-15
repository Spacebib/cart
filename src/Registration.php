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
    use Serializable;

    const SUMMARY = 'summary';

    private $participants;

    private $errors;

    /**
     * Registration constructor.
     * @param array $participants
     */
    public function __construct(array $participants)
    {
        $this->guardParticipants($participants);

        $this->participants = $participants;
    }

    /**
     * return a list of form fields
     */
    public function renderParticipantForm($trackId)
    {
        $participant = $this->getParticipantByTrackId($trackId);

        $form = $participant->getForm();

        $wasTouched = $participant->isTouched();

        $participant->setIsTouched(true);

        if ($wasTouched) {
            return $form->getData();
        }

        return $this->initialViewData($participant);
    }

    /**
     * @param $trackId
     * @param array $data
     * @return boolean
     */
    public function fillParticipantForm($trackId, array $data)
    {
        $participant = $this->getParticipantByTrackId($trackId);

        $form = $participant->getForm();

        if (!$form->fill($data)) {

            $this->errors[$trackId] = $form->getErrors();

            return false;
        }

        $participant->setIsDirty(true);

        $participant->setIsCompleted(true);

        $this->errors[$trackId] = [];

        return true;
    }

    public function getErrors($trackId)
    {
        return $this->errors[$trackId];
    }

    /**
     * @return integer / string
     */
    public function redirectTo()
    {
        $inCompletedParticipants = array_filter($this->participants, function (Participant $participant) {
            return !$participant->isCompleted();
        });

        if (empty($inCompletedParticipants)) {
            return self::SUMMARY;
        }

        return ($first = array_shift($inCompletedParticipants))->getTrackId();
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

    private function guardParticipants(array $participants)
    {
        $trackIds = array_map(function (Participant $participant) {
            return $participant->getTrackId();
        }, $participants);

        if (count(array_unique($trackIds)) != count($participants)) {
            throw new \LogicException(sprintf(
                'Invalid track IDs: %s',
                json_encode($trackIds)
            ));
        }
    }

    private function initialViewData($participant)
    {
        return array_combine(
            $participant->getForm()->getFields(),
            array_fill(0, count($participant->getForm()->getFields()), '')
        );
    }
}