<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 13/3/18
 * Time: 5:55 PM
 */

namespace Dilab\Cart;

use Dilab\Cart\Donation\Donation;
use Dilab\Cart\Entitlements\Entitlement;
use Dilab\Cart\Entitlements\Variant;
use Dilab\Cart\Products\Product;
use Dilab\Cart\Rules\RuleAge;
use Dilab\Cart\Rules\RuleEmail;
use Dilab\Cart\Rules\RuleGender;
use Dilab\Cart\Rules\RuleLength;
use Dilab\Cart\Rules\RuleNric;
use Dilab\Cart\Traits\CartHelper;

class Event
{
    use CartHelper;

    private $id;

    private $name;

    private $currency;

    private $serviceFee;

    private $categories;

    private $products;

    /**
     * Event constructor.
     *
     * @param $id
     * @param $name
     * @param $currency
     * @param $serviceFee
     * @param $categories
     * @param $products
     */
    public function __construct($id, $name, $currency, $serviceFee, $categories, $products)
    {
        $this->id = $id;
        $this->name = $name;
        $this->currency = $currency;
        $this->serviceFee = $serviceFee;
        $this->categories = $categories;
        $this->products = $products;
    }

    public static function init($data)
    {
        $id = self::getOrFail($data, 'id');

        $name = self::getOrFail($data, 'name');

        $currency = self::getOrFail($data, 'currency');

        $serviceFeeData = self::getOrFail($data, 'service_fee');

        $categoriesData = self::getOrFail($data, 'categories');

        $categoriesData = self::filterNoPriceCategories($categoriesData);

        $serviceFee = new ServiceFee(
            $serviceFeeData['percentage'],
            Money::fromCent($currency, $serviceFeeData['fixed'])
        );

        $categories = array_map(
            function ($category) use ($currency) {
                $participantsData = self::getOrFail($category, 'participants');

                return new Category(

                    self::getOrFail($category, 'id'),
                    self::getOrFail($category, 'name'),
                    Money::fromCent($currency, self::getOrFail($category, 'price')),
                    array_map(
                        function ($participant) use ($category, $currency) {
                            return Participant::fromArray($participant, self::getOrFail($category, 'id'), $currency);
                        },
                        $participantsData,
                        array_keys($participantsData)
                    )
                );
            },
            $categoriesData,
            array_keys($categoriesData)
        );

        $products = array_map(
            function ($product) use ($currency) {
                return new Product(
                    self::getOrFail($product, 'id'),
                    self::getOrFail($product, 'name'),
                    self::getOrFail($product, 'description'),
                    self::getOrFail($product, 'image_chart'),
                    self::getOrFail($product, 'image_large'),
                    self::getOrFail($product, 'image_thumb'),
                    array_map(
                        function ($variant) use ($currency) {
                            return new \Dilab\Cart\Products\Variant(
                                self::getOrFail($variant, 'id'),
                                self::getOrFail($variant, 'name'),
                                self::getOrFail($variant, 'stock'),
                                self::getOrFail($variant, 'status'),
                                Money::fromCent($currency, self::getOrFail($variant, 'price'))
                            );
                        },
                        self::getOrFail($product, 'variants')
                    )
                );
            },
            self::getOrFail($data, 'products')
        );

        return new self($id, $name, $currency, $serviceFee, $categories, $products);
    }

    public function getCategoryById(int $categoryId): Category
    {
        $found = array_filter(
            $this->categories,
            function (Category $category) use ($categoryId) {
                return $category->getId() === $categoryId;
            }
        );

        if (count($found) == 0) {
            throw new \LogicException(
                sprintf('Category with ID %s is not found', $categoryId)
            );
        }

        return $first = array_shift($found);
    }

    /**
     * @param int $productId
     * @return Product
     */
    public function getProductById(int $productId): Product
    {
        $found = array_filter(
            $this->products,
            function (Product $product) use ($productId) {
                return $product->getId() === intval($productId);
            }
        );

        if (count($found) == 0) {
            throw new \LogicException(
                sprintf('Product with ID %s is not found', $productId)
            );
        }

        return $first = array_shift($found);
    }

    public function getProductsHasVariant()
    {
        return array_filter(
            $this->products,
            function (Product $product) {
                return !empty($product->getVariants());
            }
        );
    }

    /**
     * @param array  $rules
     * @param $fields
     * @return mixed
     */
    public static function generateRules($rules, $fields)
    {
        $rules = array_map(
            function ($condition, $name) {
                return self::getRule($name, $condition);
            },
            $rules,
            array_keys($rules)
        );

        $rules[] = new RuleEmail();

        $rules[] = new RuleLength();

        if (in_array('nric', $fields)) {
            $rules[] = new RuleNric();
        }

        return $rules;
    }

    private static function getRule($name, $condition)
    {
        switch ($name) {
            case 'age':
                return new RuleAge($condition);
            case 'gender':
                return new RuleGender($condition);
            default:
                return null;
        }
    }

    private static function filterNoPriceCategories($categories)
    {
        return array_filter(
            $categories,
            function ($category) {
                return isset($category['price']) && !is_null($category['price']);
            }
        );
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
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @return mixed
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @return ServiceFee
     */
    public function getServiceFee()
    {
        return $this->serviceFee;
    }
}
