<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 13/3/18
 * Time: 5:55 PM
 */

namespace Dilab\Cart;


class Cart
{
    private $buyerEmail;

    /**
     * @var DataStore
     */
    private $store;

    private $tickets;

    /**
     * Cart constructor.
     * @param $buyerEmail
     * @param DataStore $store
     */
    public function __construct($buyerEmail, DataStore $store)
    {
        $this->buyerEmail = $buyerEmail;
        $this->store = $store;
        $this->tickets = [];
    }

    public function addTicket(Category $category, $qty)
    {
        $tickets = array_map(function () use ($category) {

            return new Category(
                $category->getId(),
                $category->getName(),
                $category->getPrice(),
                $category->getParticipants()
            );

        }, range(1, $qty));

        $this->tickets = array_merge($this->tickets, $tickets);

        return $this;
    }

    public function getParticipants()
    {
        $participants = array_reduce($this->tickets, function ($carrier, Category $category) {
            $carrier = array_merge($carrier, $category->getParticipants());
            return $carrier;
        }, []);

        $participants = array_values($participants);

        $participants = array_map(function (Participant $participant, $key) {

            $newParticipant = new Participant(
                $participant->getId(),
                $participant->getName(),
                $participant->getRules(),
                $participant->getForm()
            );

            $newParticipant->setTrackId($key);

            return $newParticipant;

        }, $participants, array_keys($participants));

        return $participants;
    }

    public function tickets()
    {
        return $this->tickets;
    }

    public function subTotal()
    {
        if (empty($this->tickets)) {
            return null;
        }

        $currency = $this->tickets[0]->getPrice()->getCurrency();

        $ticketsSubTotal = array_reduce($this->tickets, function ($carry, Category $category) {
            return $category->getPrice()->plus($carry);
        }, Money::fromCent($currency, 0));

        return $ticketsSubTotal;
    }

    public function total()
    {
        return $this->subTotal();
    }

}