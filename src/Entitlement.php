<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/26
 * Time: 上午10:49
 */

namespace Dilab\Cart;

class Entitlement
{
    private $id;

    private $name;

    private $description;

    private $imageSmall;

    private $imageLarge;

    private $variants;

    public function __construct(
        $id,
        $name,
        $description,
        $imageSmall,
        $imageLarge,
        array $variants
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->imageSmall = $imageSmall;
        $this->imageLarge = $imageLarge;
        $this->variants = $variants;
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
    public function getImageSmall()
    {
        return $this->imageSmall;
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

    public function getSelectedVariantId()
    {
        $selectedVariants = array_filter($this->variants, function ($variant) {
            return $variant->getSelected();
        });

        if (empty($selectedVariants)) {
            return '';
        }

        return array_pop($selectedVariants)->getId();
    }

    public function setSelectedVariantId($variantId)
    {
        foreach ($this->variants as $variant) {
            if ($variant->getId() == $variantId) {
                $variant->setSelected(true);
            } else {
                $variant->setSelected(false);
            }
        }
    }
}
