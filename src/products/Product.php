<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/4/17
 * Time: 上午9:43
 */

namespace Dilab\Cart\Products;

class Product
{
    private $id;

    private $name;

    private $description;

    private $imageChart;

    private $imageLarge;

    private $imageThumb;

    private $variants;

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
        $variant = $this->getSelectedVariant();
        if ($variant) {
            return $variant->getId();
        }
        return null;
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

    public function getSelectedVariantPrice()
    {
        $variant = $this->getSelectedVariant();
        if ($variant) {
            return $variant->getPrice();
        }
        return null;
    }

    private function getSelectedVariant()
    {
        $selectedVariants = array_filter($this->variants, function (Variant $variant) {
            return $variant->getSelected();
        });

        if (empty($selectedVariants)) {
            return null;
        }

        return array_pop($selectedVariants);
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
}