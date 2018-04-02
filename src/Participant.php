<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 14/3/18
 * Time: 9:38 AM
 */

namespace Dilab\Cart;

use Dilab\Cart\Donation\Donation;

class Participant
{
    private $trackId = 0;

    private $id;

    private $name;

    private $rules;

    private $form;

    private $entitlements;

    private $isTouched = false;

    private $isDirty = false;

    private $isCompleted = false;

    private $category_id;

    private $donation;

    /**
     * Participant constructor.
     * @param $id
     * @param $name
     * @param $category_id
     * @param array $rules
     * @param Form $form
     * @param array $entitlements
     * @param null | Donation $donation
     */
    public function __construct(
        $id,
        $name,
        $category_id,
        array $rules,
        Form $form,
        array $entitlements = [],
        Donation $donation = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->rules = $rules;
        $this->form = $form;
        $this->category_id = $category_id;
        $this->entitlements = $entitlements;
        $this->donation = $donation;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }


    /**
     * @return mixed
     */
    public function getTrackId()
    {
        return $this->trackId;
    }

    /**
     * @param mixed $trackId
     */
    public function setTrackId($trackId)
    {
        $this->trackId = $trackId;
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param bool $isTouched
     */
    public function setIsTouched($isTouched)
    {
        $this->isTouched = $isTouched;
    }

    /**
     * @param bool $isDirty
     */
    public function setIsDirty($isDirty)
    {
        $this->isDirty = $isDirty;
    }

    /**
     * @param bool $isCompleted
     */
    public function setIsCompleted($isCompleted)
    {
        $this->isCompleted = $isCompleted;
    }

    /**
     * @return bool
     */
    public function isTouched()
    {
        return $this->isTouched;
    }

    /**
     * @return bool
     */
    public function isDirty()
    {
        return $this->isDirty;
    }

    /**
     * @return bool
     */
    public function isCompleted()
    {
        return $this->isCompleted;
    }

    /**
     * @return mixed
     */
    public function getCategoryId()
    {
        return $this->category_id;
    }

    /**
     * @param mixed $category_id
     */
    public function setCategoryId($category_id)
    {
        $this->category_id = $category_id;
    }

    /**
     * @return Entitlement[]
     */
    public function getEntitlements(): array
    {
        return $this->entitlements;
    }

    /**
     * @param $id
     * @return bool|Entitlement
     */
    public function getEntitlement($id)
    {
        foreach ($this->entitlements as $entitlement) {
            if ($entitlement->getId() === $id) {
                return $entitlement;
            }
        }
        return false;
    }

    /**
     * @return Donation|null
     */
    public function getDonation()
    {
        return $this->donation;
    }

    public function hasDonation()
    {
        return $this->donation instanceof Donation;
    }

    public function getDonationAmount()
    {
        return $this->donation->getAmount();
    }

    public function getShowName()
    {
        $data = $this->form->getData();

        if ($data['first_name']) {
            return $data['first_name'].' '.$data['last_name'];
        }

        return $this->name;
    }
}
