<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 13/3/18
 * Time: 5:55 PM
 */

namespace Dilab\Cart;


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

        $categories = array_map(function ($category, $i) use ($currency) {

            $participantsData = self::getWithException($category, 'participants');

            $countParticipants = count($participantsData);

            return new Category(

                self::getWithException($category, 'id'),

                self::getWithException($category, 'name'),

                Money::fromCent($currency, self::getWithException($category, 'price')),

                array_map(function ($participant, $j) use ($i, $countParticipants) {

                    return new Participant(
                        self::getWithException($participant, 'id'),
                        ($i * $countParticipants + $j),
                        self::getWithException($participant, 'name'),
                        self::getWithException($participant, 'rules'),
                        new Form(self::getWithException($participant, 'fields'))
                    );

                }, $participantsData, array_keys($participantsData))
            );

        }, $categoriesData, array_keys($categoriesData));

        return new self($id, $name, $currency, $categories);
    }

    public function getParticipants()
    {
        return array_reduce($this->categories, function ($carrier, Category $category) {

            $carrier = array_merge($carrier, $category->getParticipants());

            return $carrier;

        }, []);
    }

    public function getCategoryById($categoryId)
    {

    }

}