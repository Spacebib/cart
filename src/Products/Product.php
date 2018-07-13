<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/4/17
 * Time: 上午9:43
 */

namespace Dilab\Cart\Products;

use Dilab\Cart\Money;

class Product
{
    private $id;

    private $name;

    private $description;

    private $imageChart;

    private $imageLarge;

    private $imageThumb;
    /**
     * @var Variant[]
     */
    private $variants;

    private $participantAccessCode;

    public function __construct(
        $id,
        $name,
        $description,
        $imageChart,
        $imageLarge,
        $imageThumb,
        array $variants
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->imageChart = $imageChart;
        $this->imageLarge = $imageLarge;
        $this->imageThumb = $imageThumb;
        $this->variants = $variants;
    }

    public function getSelectedVariantId()
    {
        return optional($this->getSelectedVariant())->getId();
    }

    public function setSelectedVariantId($variantId)
    {
        foreach ($this->getAvailableVariants() as $variant) {
            if ($variant->getId() == $variantId) {
                $variant->setSelected(true);
            } else {
                $variant->setSelected(false);
            }
        }

        return $this;
    }

    /**
     * @return null | Money
     */
    public function getSelectedVariantPrice()
    {
        return optional($this->getSelectedVariant())->getPrice();
    }

    public function getSelectedVariant()
    {
        foreach ($this->variants as $variant) {
            if ($variant->getSelected()) {
                return $variant;
            }
        }

        return null;
    }

    /**
     * @return Variant[]
     */
    public function getAvailableVariants(): array
    {
        return array_filter(
            $this->variants,
            function (Variant $variant) {
                return $variant->isAvailable();
            }
        );
    }

    public function getCurrency()
    {
        return $this->variants[0]->getPrice()->getCurrency();
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
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getImageChart()
    {
        return $this->imageChart;
    }

    /**
     * @return mixed
     */
    public function getImageLarge()
    {
        return $this->imageLarge;
    }

    /**
     * @return array
     */
    public function getVariants(): array
    {
        return $this->variants;
    }

    /**
     * @return mixed
     */
    public function getImageThumb()
    {
        return $this->imageThumb;
    }

    /**
     * @return mixed
     */
    public function getParticipantAccessCode()
    {
        return $this->participantAccessCode;
    }

    /**
     * @param mixed $participantAccessCode
     * @return $this
     */
    public function setParticipantAccessCode($participantAccessCode)
    {
        $this->participantAccessCode = $participantAccessCode;
        return $this;
    }
}
