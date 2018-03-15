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

    private $tickets;

    /**
     * Cart constructor.
     * @param $buyerEmail
     */
    public function __construct($buyerEmail)
    {
        $this->buyerEmail = $buyerEmail;
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

    public function serialize()
    {
        return [
            'buyer_email' => $this->buyerEmail,
            'tickets' => array_map(function (Category $category) {
                return $category->serialize();
            })
        ];
    }

    public static function deserialize($data)
    {

    }

}