<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/4/12
 * Time: 下午2:21
 */

namespace Dilab\Cart;

class Coupon
{
    private $id;

    private $categoryIds;

    private $discountType;

    private $discountRate;

    private $code;

    /**
     * Coupon constructor.
     * @param $id
     * @param array $categoryIds
     * @param $discountType
     * @param $discountRate
     * @param $code
     */
    public function __construct($id, array $categoryIds, $discountType, $discountRate, $code)
    {
        $this->id = $id;
        $this->categoryIds = $categoryIds;
        $this->discountType = $discountType;
        $this->discountRate = $discountRate;
        $this->code = $code;
    }

    public function apply(Money $amount)
    {
        if ($this->discountType === DiscountType::FIXVALUE) {
            return $amount->minus(Money::fromCent(
                $amount->getCurrency(),
                $this->discountRate
            ));
        }

        if ($this->discountType === DiscountType::PERCENTAGEOFF) {
            return Money::fromCent(
                $amount->getCurrency(),
                intval($amount->toCent()*$this->discountRate/100)
            );
        }

        return $amount;
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
}
