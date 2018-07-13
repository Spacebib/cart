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
    /**
     * @var  Money
     */
    private $discount;

    private $stock;
    /**
     * @var Discounter
     */
    private $discounter;

    /**
     * Coupon constructor.
     *
     * @param $id
     * @param array        $categoryIds
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

        $this->discounter = DiscounterFactory::build($this->discountType, $this->discountRate);
    }

    public function apply(Money $amount): Money
    {
        $this->discounter->execute($amount);

        $this->reduceStock();

        $this->discount = $this->discounter->getDiscount();

        return $this->discounter->getDiscountedPrice();
    }

    public function canApply(int $categoryId): bool
    {
        return $this->stock > 0 && in_array($categoryId, $this->getCategoryIds());
    }

    private function reduceStock(): void
    {
        $this->stock--;
    }

    /**
     * @return Money
     */
    public function getDiscount(): Money
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
