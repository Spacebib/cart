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
     * @param $data
     * @param $categoryId
     * @param $currency
     * @return $this
     */
    public static function fromArray($data, $categoryId, $currency)
    {
        $participant = new self(
            self::getOrFail($data, 'id'),
            self::getOrFail($data, 'name'),
            $categoryId,
            self::getOrFail($data, 'rules'),
            new Form(
                Event::generateRules(
                    self::getOrFail($data, 'rules'),
                    self::getOrFail($data, 'fields')
                ),
                self::getOrFail($data, 'fields')
            ),
            array_map(
                function ($entitlement) use ($currency) {
                    return new Entitlement(
                        self::getOrFail($entitlement, 'id'),
                        self::getOrFail($entitlement, 'name'),
                        self::getOrFail($entitlement, 'description'),
                        self::getOrFail($entitlement, 'image_chart'),
                        self::getOrFail($entitlement, 'image_large'),
                        self::getOrFail($entitlement, 'image_thumb'),
                        self::getOrFail($entitlement, 'visible'),
                        array_map(
                            function ($variant) {
                                return new Variant(
                                    self::getOrFail($variant, 'id'),
                                    self::getOrFail($variant, 'name'),
                                    self::getOrFail($variant, 'status'),
                                    self::getOrFail($variant, 'stock')
                                );
                            },
                            self::getOrFail($entitlement, 'variants')
                        )
                    );
                },
                data_get($data, 'entitlements', [])
            ),
            array_map(
                function ($fundraise) use ($currency) {
                    return new Donation(
                        self::getOrFail($fundraise, 'id'),
                        self::getOrFail($fundraise, 'name'),
                        new \Dilab\Cart\Donation\Form(
                            self::getOrFail($fundraise, 'fields'),
                            [
                                'min' => self::getOrFail($fundraise, 'min'),
                                'max' => self::getOrFail($fundraise, 'max'),
                                'required' => self::getOrFail($fundraise, 'required'),
                            ]
                        ),
                        $currency
                    );
                },
                data_get($data, 'fundraises', [])
            ),
            new CustomFields(data_get($data, 'custom_fields', []))
        );

        return $participant;
    }

    public function formatFieldsData()
    {
        $data['fields'] = $this->getForm()->getData();
        // handle email_confirmation
        unset($data['fields']['email_confirmation']);
        // handle participant_id
        $data['fields']['participant_id'] = $this->getId();
        // handle grouping_num
        $data['fields']['grouping_num'] = $this->getGroupNum();
        // handle access_code
        $data['fields']['access_code'] = $this->getAccessCode();
        // handle address
        if (isset($data['fields']['address_sg_standard'])) {
            $data['fields']['address'] = $data['fields']['address_sg_standard'];
            unset($data['fields']['address_sg_standard']);
        }
        // handle custom-fields
        $data['fields']['custom_fields'] = '';
        if ($this->getCustomFields()) {
            $customFields = $this->getCustomFields()->getFields();
            $data['fields']['custom_fields'] = $customFields;
        }

        foreach ($data['fields'] as $field => $value) {
            if ($field === 'dob') {
                $value = \DateTime::createFromFormat(
                    'd-m-Y',
                    implode("-", array_values($value))
                )->format('Y-m-d');
            } elseif (is_array($value)) {
                $value = json_encode($value);
            }
            $data['fields'][$field] = $value;
        }

        return $data['fields'];
    }

    public function toEntryArray()
    {
        $data = [];

        $data['fields'] = $this->getForm()->getData();
        $data['entitlements'] = [];
        $data['fundraises'] = [];
        // handle email_confirmation
        unset($data['fields']['email_confirmation']);
        // handle participant_id
        $data['fields']['participant_id'] = $this->getId();
        // handle grouping_num
        $data['fields']['grouping_num'] = $this->getGroupNum();
        // handle access_code
        $data['fields']['access_code'] = $this->getAccessCode();
        // handle address
        if (isset($data['fields']['address_sg_standard'])) {
            $data['fields']['address'] = $data['fields']['address_sg_standard'];
            unset($data['fields']['address_sg_standard']);
        }

        $entitlements = array_filter(
            $this->getEntitlements(),
            function (Entitlement $entitlement) {
                return $entitlement->getSelectedVariantId();
            }
        );

        foreach ($entitlements as $entitlement) {
            $data['entitlements'][] = [
                'entitlement_variant_id' => $entitlement->getSelectedVariantId(),
            ];
        }

        $fundraises = $this->getFundraises();

        foreach ($fundraises as $fundraise) {
            $data['fundraises'][] = [
                'fundraise_id' => $fundraise->getId(),
                'amount' => array_get($fundraise->getForm()->getData(), 'fundraise_amount'),
                'remark' => array_get($fundraise->getForm()->getData(), 'fundraise_remark')
            ];
        }

        if ($this->getCustomFields()) {
            $customFields = $this->getCustomFields()->getFields();
            $data['custom_fields'] = $customFields;
        }

        return $data;
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

    public function getEntitlementsHasAvailableVariant()
    {
        return array_filter(
            $this->entitlements,
            function (Entitlement $entitlement) {
                return !empty($entitlement->getAvailableVariants());
            }
        );
    }

    public function getAvailableEntitlements()
    {
        return array_filter(
            $this->entitlements,
            function (Entitlement $entitlement) {
                return $entitlement->isVisible() && !empty($entitlement->getAvailableVariants());
            }
        );
    }

    /**
     * @param $id
     * @return null|Entitlement
     */
    public function getEntitlement($id)
    {
        foreach ($this->entitlements as $entitlement) {
            if ($entitlement->getId() === $id) {
                return $entitlement;
            }
        }
        return null;
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
