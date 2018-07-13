<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 13/3/18
 * Time: 5:59 PM
 */

namespace Dilab\Cart;

use Dilab\Cart\Donation\Donation;
use Dilab\Cart\Entitlements\Entitlement;
use Dilab\Cart\Rules\RuleNric;
use Dilab\Cart\Traits\CartHelper;
use Dilab\Cart\Traits\Serializable;

class Registration
{
    use Serializable;

    use CartHelper;

    const SUMMARY = 'summary';

    const ADDON = 'add_on';

    private $participants;

    private $errors;

    private $hasProducts;

    /**
     * Registration constructor.
     *
     * @param array       $participants
     * @param $hasProducts
     */
    public function __construct(array $participants, bool $hasProducts = false)
    {
        $this->guardParticipants($participants);

        $this->participants = $participants;
        $this->hasProducts = $hasProducts;
    }

    public function getTrackIdByParticipantId($participantId)
    {
        foreach ($this->participants as $participant) {
            if ($participant->getId() === $participantId) {
                return $participant->getTrackId();
            }
        }
        return null;
    }

    public function renderParticipant(int $trackId)
    {
        $participant = $this->getParticipantByTrackId($trackId);

        $form = $this->renderForm($participant);

        $entitlements = $this->renderEntitlements($participant);

        $fundraises = $this->renderDonation($participant);

        $customFields = $this->renderCustomFields($participant);

        return compact('form', 'entitlements', 'fundraises', 'customFields');
    }

    public function fillParticipant(int $trackId, array $data)
    {
        // truncate errors first
        $this->errors[$trackId] = [];

        $participant = $this->getParticipantByTrackId($trackId);

        $fillForm = $this->fillForm($participant, $data['form']);
        $fillEntitlements = $this->fillEntitlements($participant, data_get($data, 'entitlements', []));
        $fillDonation = $this->fillDonation($participant, data_get($data, 'donations', []));
        $fillCustomFields = $this->fillCustomFields($participant, data_get($data, 'customFields', []));

        $participant->setIsDirty(true);

        if ($fillForm && $fillEntitlements && $fillDonation && $fillCustomFields) {
            $participant->setIsCompleted(true);

            return true;
        }

        $participant->setIsCompleted(false);

        return false;
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
        $inCompletedParticipants = $this->inCompletedParticipants();

        if (count($inCompletedParticipants) === 0) {
            return $this->hasProducts ? self::ADDON : self::SUMMARY;
        }

        return ($first = array_shift($inCompletedParticipants))->getTrackId();
    }

    public function allIsCompleted()
    {
        return count($this->inCompletedParticipants()) === 0;
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

    public function getParticipantsByCategoryId($categoryId)
    {
        return array_filter(
            $this->participants,
            function (Participant $participant) use ($categoryId) {
                return $participant->getCategoryId()  === $categoryId;
            }
        );
    }

    /**
     * return a list of form fields
     *
     * @param Participant $participant
     * @return mixed
     */
    public function renderForm(Participant $participant)
    {
        $form = $participant->getForm();

        $wasTouched = $participant->isTouched();

        $participant->setIsTouched(true);

        if ($wasTouched) {
            return $form->getData();
        }
        return $form->getFields();
    }

    /**
     * @param Participant $participant
     * @param array $data
     * @return bool
     */
    public function fillForm(Participant $participant, array $data)
    {
        $form = $participant->getForm();

        $form->updateNRICRule($this, $participant);

        if ($form->fill($data)) {
            return true;
        }

        $this->setErrorsByTrackId($participant->getTrackId(), $form->getErrors());

        return false;
    }

    public function renderEntitlements(Participant $participant)
    {
        $entitlements = $participant->getEntitlementsHasVariant();

        return $entitlements;
    }

    public function fillEntitlements(Participant $participant, array $data)
    {
        list($flag, $errors) = $this->validateEntitlementData($data, $participant);

        $this->setErrorsByTrackId($participant->getTrackId(), ['entitlements' => $errors]);

        return $flag;
    }

    public function renderDonation(Participant $participant)
    {
        $fundraises = $participant->getFundraises();

        return $fundraises;
    }

    public function fillDonation(Participant $participant, array $data)
    {
        $flag = true;

        $fundraises = $participant->getFundraises();

        foreach ($fundraises as $donation) {
            if (! $this->validateDonationData($participant->getTrackId(), $data, $donation)) {
                $flag = false;
            }
        }

        return $flag;
    }

    private function renderCustomFields(Participant $participant)
    {
        $form = $participant->getCustomFields();

        return $form->getFields();
    }

    private function fillCustomFields(Participant $participant, array $data)
    {
        $form = $participant->getCustomFields();

        return $form->fill($data);
    }

    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * @param $trackId
     * @return Participant
     */
    public function getParticipantByTrackId($trackId)
    {
        return $this->participants[$trackId];
    }

    private function guardParticipants(array $participants)
    {
        $trackIds = array_map(
            function (Participant $participant) {
                return $participant->getTrackId();
            },
            $participants
        );

        if (count(array_unique($trackIds)) != count($participants)) {
            throw new \LogicException(
                sprintf(
                    'Invalid track IDs: %s',
                    json_encode($trackIds)
                )
            );
        }
    }

    private function setErrorsByTrackId($trackId, $error)
    {
        if (isset($this->errors[$trackId])) {
            $this->errors[$trackId] = array_merge(
                $this->errors[$trackId],
                $error
            );
        } else {
            $this->errors[$trackId] = $error;
        }
    }

    /**
     * validate entitlement request data
     *
     * @param array $data
     * @param $participant
     * @return array
     */
    private function validateEntitlementData(array $data, Participant $participant): array
    {
        $flag = true;
        $errors = null;
        $requestIds = array_keys($data);
        $entitlementIds = array_map(
            function (Entitlement $entitlement) {
                return $entitlement->getId();
            },
            $participant->getEntitlementsHasAvailableVariant()
        );
        // determine if has enough entitlements
        if ($lacks = array_diff($entitlementIds, $requestIds)) {
            array_map(
                function ($lack) {
                    $errors[$lack] = 'Please select an option for each item';
                },
                $lacks
            );
            $flag = false;
        }

        // determine if has variantId for each valid entitlement
        foreach ($data as $entitlementId => $variantId) {
            if (!in_array($entitlementId, $entitlementIds)) {
                continue;
            }

            $entitlement = $participant->getEntitlement($entitlementId);

            if (!$variantId) {
                $errors[$entitlementId] = 'Please select an option for each item';
                $flag = false;
                continue;
            } else {
                $entitlement->setSelectedVariantId($variantId);
            }
        }

        return array($flag, $errors);
    }

    /**
     * @param $trackId
     * @param array $data
     * @param $donation
     * @return bool
     */
    private function validateDonationData(int $trackId, array $data, Donation $donation): bool
    {
        $form = $donation->getForm();
        $donationId = $donation->getId();

        if ($form->fill(data_get($data, $donationId, []), $donation->getId())) {
            return true;
        }

        $this->setErrorsByTrackId($trackId, $form->getErrors());

        return false;
    }

    private function inCompletedParticipants()
    {
        return array_filter(
            $this->participants,
            function (Participant $participant) {
                return !$participant->isCompleted();
            }
        );
    }
}
