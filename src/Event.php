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

    private $categories;

    private $products;

    /**
     * Event constructor.
     * @param $id
     * @param $name
     * @param $currency
     * @param $categories
     * @param $products
     */
    public function __construct($id, $name, $currency, $categories, $products)
    {
        $this->id = $id;
        $this->name = $name;
        $this->currency = $currency;
        $this->categories = $categories;
        $this->products = $products;
    }

    public static function init($data)
    {
        $id = self::getWithException($data, 'id');

        $name = self::getWithException($data, 'name');

        $currency = self::getWithException($data, 'currency');

        $categoriesData = self::getWithException($data, 'categories');

        $categoriesData = self::filterNoPriceCategories($categoriesData);

        $categories = array_map(function ($category) use ($currency) {
            $participantsData = self::getWithException($category, 'participants');

            return new Category(

                self::getWithException($category, 'id'),
                self::getWithException($category, 'name'),
                Money::fromCent($currency, self::getWithException($category, 'price')),
                array_map(
                    function ($participant) use ($category, $currency) {

                        return new Participant(
                            self::getWithException($participant, 'id'),
                            self::getWithException($participant, 'name'),
                            self::getWithException($category, 'id'),
                            self::getWithException($participant, 'rules'),
                            new Form(
                                self::generateRules(
                                    self::getWithException($participant, 'rules'),
                                    self::getWithException($participant, 'fields')
                                ),
                                self::getWithException($participant, 'fields')
                            ),
                            array_map(function ($entitlement) use ($currency) {
                                return new Entitlement(
                                    self::getWithException($entitlement, 'id'),
                                    self::getWithException($entitlement, 'name'),
                                    self::getWithException($entitlement, 'description'),
                                    self::getWithException($entitlement, 'image_chart'),
                                    self::getWithException($entitlement, 'image_large'),
                                    self::getWithException($entitlement, 'image_thumb'),
                                    array_map(function ($variant) {
                                        return new Variant(
                                            self::getWithException($variant, 'id'),
                                            self::getWithException($variant, 'name'),
                                            self::getWithException($variant, 'status'),
                                            self::getWithException($variant, 'stock')
                                        );
                                    }, self::getWithException($entitlement, 'variants'))
                                );
                            }, self::getOrEmptyArray($participant, 'entitlements')),
                            array_map(function ($fundraise) use ($currency) {
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
                            }, self::getOrEmptyArray($participant, 'fundraises')),
                            new CustomFields(self::getOrEmptyArray($participant, 'custom_fields'))
                        );
                    },
                    $participantsData,
                    array_keys($participantsData)
                )
            );
        }, $categoriesData, array_keys($categoriesData));

        $products = array_map(function ($product) use ($currency) {
            return new Product(
                self::getWithException($product, 'id'),
                self::getWithException($product, 'name'),
                self::getWithException($product, 'description'),
                self::getWithException($product, 'image_chart'),
                self::getWithException($product, 'image_large'),
                self::getWithException($product, 'image_thumb'),
                array_map(function ($variant) use ($currency) {
                    return new \Dilab\Cart\Products\Variant(
                        self::getWithException($variant, 'id'),
                        self::getWithException($variant, 'name'),
                        self::getWithException($variant, 'stock'),
                        self::getWithException($variant, 'status'),
                        Money::fromCent($currency, self::getWithException($variant, 'price'))
                    );
                }, self::getWithException($product, 'variants'))
            );
        }, self::getWithException($data, 'products'));

        return new self($id, $name, $currency, $categories, $products);
    }

    public function getCategoryById($categoryId)
    {
        $found = array_filter($this->categories, function (Category $category) use ($categoryId) {
            return $category->getId() === $categoryId;
        });

        if (count($found) == 0) {
            throw new \LogicException(
                sprintf('Category with ID %s is not found', $categoryId)
            );
        }

        return $first = array_shift($found);
    }

    /**
     * @param $productId
     * @return Product
     */
    public function getProductById($productId)
    {
        $found = array_filter($this->products, function (Product $product) use ($productId) {
            return $product->getId() === intval($productId);
        });

        if (count($found) == 0) {
            throw new \LogicException(
                sprintf('Product with ID %s is not found', $productId)
            );
        }

        return $first = array_shift($found);
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

    public function getProductsHasVariant()
    {
        return array_filter($this->products, function (Product $product) {
            return !empty($product->getVariants());
        });
    }

    /**
     * @param array $rules
     * @param $fields
     * @return mixed
     */
    private static function generateRules($rules, $fields)
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
        return array_filter($categories, function ($category) {
            return isset($category['price']) && !is_null($category['price']);
        });
    }
}
