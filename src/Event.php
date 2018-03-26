<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 13/3/18
 * Time: 5:55 PM
 */

namespace Dilab\Cart;

use Dilab\Cart\Rules\RuleAge;
use Dilab\Cart\Rules\RuleEmail;
use Dilab\Cart\Rules\RuleGender;
use Dilab\Cart\Rules\RuleLength;
use Dilab\Cart\Rules\RuleNric;

class Event
{
    use CartHelper;

    private $id;

    private $name;

    private $currency;

    private $categories;

    /**
     * Event constructor.
     * @param $id
     * @param $name
     * @param $currency
     * @param $categories
     */
    public function __construct($id, $name, $currency, $categories)
    {
        $this->id = $id;
        $this->name = $name;
        $this->currency = $currency;
        $this->categories = $categories;
    }

    public static function init($data)
    {
        $id = self::getWithException($data, 'id');

        $name = self::getWithException($data, 'name');

        $currency = self::getWithException($data, 'currency');

        $categoriesData = self::getWithException($data, 'categories');

        $categories = array_map(function ($category) use ($currency) {
            $participantsData = self::getWithException($category, 'participants');

            return new Category(

                self::getWithException($category, 'id'),
                self::getWithException($category, 'name'),
                Money::fromCent($currency, self::getWithException($category, 'price')),
                array_map(
                    function ($participant) use ($category) {
                        return new Participant(
                            self::getWithException($participant, 'id'),
                            self::getWithException($participant, 'name'),
                            self::getWithException($category, 'id'),
                            self::getWithException($participant, 'rules'),
                            new Form(
                                self::generateRules(
                                    self::getWithException($participant, 'rules')
                                ),
                                self::getWithException($participant, 'fields')
                            ),
                            array_map(function ($entitlement) {
                                return new Entitlement(
                                    self::getWithException($entitlement, 'id'),
                                    self::getWithException($entitlement, 'name'),
                                    self::getWithException($entitlement, 'description'),
                                    self::getWithException($entitlement, 'image_small'),
                                    self::getWithException($entitlement, 'image_large'),
                                    array_map(function ($variant) {
                                        return new Variant(
                                            self::getWithException($variant, 'id'),
                                            self::getWithException($variant, 'name'),
                                            self::getWithException($variant, 'status')
                                        );
                                    }, self::getWithException($entitlement, 'variants'))
                                );
                            }, self::getWithException($participant, 'entitlements'))
                        );
                    },
                    $participantsData,
                    array_keys($participantsData)
                )
            );
        }, $categoriesData, array_keys($categoriesData));

        return new self($id, $name, $currency, $categories);
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
     * @param array $rules
     * @return mixed
     */
    private static function generateRules($rules)
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
        return $rules;
    }

    private static function getRule($name, $condition)
    {
        switch ($name) {
            case 'age':
                return new RuleAge($condition);
            case 'gender':
                return new RuleGender($condition);
            case 'nric':
                return new RuleNric();
            default:
                return null;
        }
    }
}
