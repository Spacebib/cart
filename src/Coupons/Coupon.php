<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/4/12
 * Time: 下午2:21
 */

namespace Dilab\Cart\Coupons;

use Dilab\Cart\Exceptions\InvalidDiscountTypeException;
use Dilab\Cart\Money;

class Coupon
{
    private $id;

    private $categoryIds;

    private $discountType;

    private $discountRate;

    private $code;
    /** @var  Money */
    private $discount;

    private $stock;

    /**
     * Coupon constructor.
     * @param $id
     * @param array $categoryIds
     * @param $discountType
     * @param $discountRate
     * @param $code
     * @param $stock
     */
    public function __construct(
        $id,
        array $categoryIds,
        $discountType,
        $discountRate,
        $code,
        $stock
    ) {
        $this->id = $id;
        $this->categoryIds = $categoryIds;
        $this->discountType = $discountType;
        $this->discountRate = $discountRate;
        $this->code = $code;
        $this->stock = $stock;
    }

    public function apply(Money $amount)
    {
        $this->guardDiscount($amount);

        $this->stock--;

        if ($this->discountType === DiscountType::FIXVALUE) {
            $this->discount = Money::fromCent($amount->getCurrency(), $this->discountRate);

            return $amount->minus(Money::fromCent(
                $amount->getCurrency(),
                $this->discountRate
            ));
        } elseif ($this->discountType === DiscountType::PERCENTAGEOFF) {
            $this->discount = Money::fromCent(
                $amount->getCurrency(),
                $amount->toCent()*$this->discountRate/100
            );

            return Money::fromCent(
                $amount->getCurrency(),
                intval($amount->toCent() - $amount->toCent()*$this->discountRate/100)
            );
        }

        return $amount;
    }

    public function canApply($categoryId)
    {
        return $this->stock > 0 && in_array($categoryId, $this->getCategoryIds());
    }

    private function guardDiscount(Money $amount)
    {
        if (! in_array($this->discountType, DiscountType::types())) {
            InvalidDiscountTypeException::throw(
                sprintf('invalid discount type %s', $this->discountType)
            );
        }

        if ($this->discountType === DiscountType::PERCENTAGEOFF) {
            if ($this->discountRate > 100) {
                $this->discountRate = 100;
            }
        } elseif ($this->discountType === DiscountType::FIXVALUE) {
            if ($this->discountRate > $amount->toCent()) {
                $this->discountRate = $amount->toCent();
            }
        }
    }

    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getCategoryIds()
    {
        return $this->categoryIds;
    }

    /**
     * @param mixed $categoryIds
     */
    public function setCategoryIds($categoryIds)
    {
        $this->categoryIds = $categoryIds;
    }

    /**
     * @return mixed
     */
    public function getDiscountType()
    {
        return $this->discountType;
    }

    /**
     * @param mixed $discountType
     */
    public function setDiscountType($discountType)
    {
        $this->discountType = $discountType;
    }

    /**
     * @return mixed
     */
    public function getDiscountRate()
    {
        return $this->discountRate;
    }

    /**
     * @param mixed $discountRate
     */
    public function setDiscountRate($discountRate)
    {
        $this->discountRate = $discountRate;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getStock()
    {
        return $this->stock;
    }
}
