<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/22
 * Time: 上午9:08
 */

namespace Dilab\Cart\Rules;

use Dilab\Cart\Participant;
use Dilab\Cart\Registration;

class RuleNric implements Rule
{
    private $errors = [];
    /**
     * @var Registration
     */
    private $registration;

    private $category_id;

    private $trackId;

    /**
     * @param mixed $registration
     */
    public function setRegistration(Registration $registration)
    {
        $this->registration = $registration;
    }

    /**
     * @param mixed $trackId
     */
    public function setTrackId($trackId)
    {
        $this->trackId = $trackId;
    }

    /**
     * @param mixed $category_id
     */
    public function setCategoryId($category_id)
    {
        $this->category_id = $category_id;
    }

    public function valid($data)
    {
        $nric = $data['nric'];
        /**
         * @var Participant[] $participants
         */
        $participants = $this->registration->getParticipantsByCategoryId($this->category_id);

        foreach ($participants as $participant) {
            if ($this->trackId != $participant->getTrackId() &&
                isset($participant->getForm()->getData()['nric']) &&
                $participant->getForm()->getData()['nric'] === $nric
            ) {
                $this->errors = [
                    'nric' => sprintf(
                        'It seems like %s has already been used to register for this category. 
                        Please check your email inbox to get the confirmation slip.',
                        $nric
                    )
                ];
                return false;
            }
        }

        $this->errors = [];
        return true;
    }

    public function errors()
    {
        return $this->errors;
    }
}
