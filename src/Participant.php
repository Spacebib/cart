<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 14/3/18
 * Time: 9:38 AM
 */

namespace Dilab\Cart;

use Dilab\Cart\Donation\Donation;
use Dilab\Cart\Entitlements\Entitlement;
use Dilab\Cart\Entitlements\Variant;
use Dilab\Cart\Traits\CartHelper;

class Participant
{
    use CartHelper;

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

    private $fundraises;

    /**
     * @var CustomFields
     */
    private $customFields;

    private $groupNum;

    private $accessCode;

    /**
     * Participant constructor.
     *
     * @param    $id
     * @param    $name
     * @param    $category_id
     * @param    array        $rules
     * @param    Form         $form
     * @param    array        $entitlements
     * @param    array        $fundraises
     * @param    CustomFields $customFields
     * @internal param Donation|null $donation
     */
    public function __construct(
        $id,
        $name,
        $category_id,
        array $rules,
        Form $form,
        array $entitlements = [],
        array $fundraises = [],
        CustomFields $customFields = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->rules = $rules;
        $this->form = $form;
        $this->category_id = $category_id;
        $this->entitlements = $entitlements;
        $this->fundraises = $fundraises;
        $this->customFields = $customFields;
    }

    /**
     * @param $entry
     * @return $this
     */
    public static function fromEntry($entry)
    {
        $rules = [
            'age' => $entry->participant->allow_age,
            'gender' => $entry->participant->allow_gender,
        ];

        $currency = $entry->participant->category->event->currency;

        $participant = new self(
            $entry->participant_id,
            $entry->participant->name,
            $entry->participant->category_id,
            $rules,
            new Form(
                Event::generateRules($rules, $entry->participant->registrationForm->fields),
                $entry->participant->registrationForm->fields
            ),
            array_map(
                function ($entitlement) use ($currency) {
                    return new Entitlement(
                        self::getWithException($entitlement, 'id'),
                        self::getWithException($entitlement, 'name'),
                        self::getWithException($entitlement, 'description'),
                        self::getWithException($entitlement, 'image_chart'),
                        self::getWithException($entitlement, 'image_large'),
                        self::getWithException($entitlement, 'image_thumb'),
                        array_map(
                            function ($variant) {
                                return new Variant(
                                    self::getWithException($variant, 'id'),
                                    self::getWithException($variant, 'name'),
                                    self::getWithException($variant, 'status'),
                                    self::getWithException($variant, 'stock')
                                );
                            },
                            self::getWithException($entitlement, 'variants')
                        )
                    );
                },
                $entry->participant->entitlements->toArray()
            ),
            array_map(
                function ($fundraise) use ($currency) {
                    return new Donation(
                        self::getWithException($fundraise, 'id'),
                        self::getWithException($fundraise, 'name'),
                        new \Dilab\Cart\Donation\Form(
                            self::getWithException($fundraise, 'fields'),
                            [
                                'min' => self::getWithException($fundraise, 'min'),
                                'max' => self::getWithException($fundraise, 'max'),
                                'required' => self::getWithException($fundraise, 'required'),
                            ]
                        ),
                        $currency
                    );
                },
                $entry->participant->fundraises->toArray()
            ),
            new CustomFields($entry->participant->registrationForm->custom_fields)
        );

        $participant->setAccessCode($entry->access_code);

        $participant->setGroupNum($entry->grouping_num);

        return $participant;
    }

    public function getEntitlementsHasVariant()
    {
        return array_filter(
            $this->entitlements,
            function (Entitlement $entitlement) {
                return !empty($entitlement->getVariants());
            }
        );
    }

    public function getEntitlementsHasVariantHasStock()
    {
        return array_filter(
            $this->entitlements,
            function (Entitlement $entitlement) {
                return !empty($entitlement->getVariantsHasStock());
            }
        );
    }

    public function getEntitlementsHasAvailableVariant()
    {
        return array_filter(
            $this->entitlements,
            function (Entitlement $entitlement) {
                return !empty($entitlement->getVariantsAvailable());
            }
        );
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
     * @return Donation[]
     */
    public function getFundraises()
    {
        return $this->fundraises;
    }

    public function hasFundraises()
    {
        if (! isset($this->fundraises[0])) {
            return false;
        }
        $donation = $this->fundraises[0];
        return $donation instanceof Donation;
    }

    public function getFundraisesAmount()
    {
        $donation = $this->fundraises[0];

        return array_reduce(
            $this->fundraises,
            function ($carry, Donation $donation) {
                return $donation->getAmount()->plus($carry);
            },
            Money::fromCent($donation->getCurrency(), 0)
        );
    }

    public function getShowName()
    {
        $data = $this->form->getData();

        if (array_has($data, 'first_name')) {
            return implode(
                " ",
                [
                    data_get($data, 'first_name', ''),
                    data_get($data, 'middle_name', ''),
                    data_get($data, 'last_name', ''),
                ]
            );
        }

        return $this->name;
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
     * @return CustomFields
     */
    public function getCustomFields(): CustomFields
    {
        return $this->customFields;
    }

    /**
     * @param CustomFields $customFields
     */
    public function setCustomFields(CustomFields $customFields)
    {
        $this->customFields = $customFields;
    }

    /**
     * @return string
     */
    public function getGroupNum(): string
    {
        return $this->groupNum;
    }

    /**
     * @param string $groupNum
     */
    public function setGroupNum(string $groupNum)
    {
        $this->groupNum = $groupNum;
    }

    /**
     * @return string
     */
    public function getAccessCode(): string
    {
        return $this->accessCode;
    }

    /**
     * @param string $accessCode
     */
    public function setAccessCode(string $accessCode)
    {
        $this->accessCode = $accessCode;
    }
}
