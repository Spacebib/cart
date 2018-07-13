<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/26
 * Time: 上午10:49
 */

namespace Dilab\Cart\Entitlements;

class Entitlement
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

    /**
     * @return array
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

    public function getSelectedVariantId()
    {
        foreach ($this->variants as $variant) {
            if ($variant->getSelected()) {
                return $variant->getId();
            }
        }

        return '';
    }

    public function setSelectedVariantId($variantId)
    {
        // For handling edit,
        // allow to select unavailable variant here
        foreach ($this->variants as $variant) {
            if ($variant->getId() == $variantId) {
                $variant->setSelected(true);
            } else {
                $variant->setSelected(false);
            }
        }
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
