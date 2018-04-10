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
    use Serializable;

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
                array_map(function ($participant) {
                    return clone $participant;
                }, $category->getParticipants())
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
                $participant->getCategoryId(),
                $participant->getRules(),
                clone $participant->getForm(),
                $participant->getEntitlements(),
                $participant->getFundraises()
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

    public function getBuyerEmail()
    {
        return $this->buyerEmail;
    }

    /**
     * @return null | Money
     */
    public function subTotal()
    {
        if (empty($this->tickets)) {
            return null;
        }

        $currency = $this->tickets[0]->getPrice()->getCurrency();

        $ticketsSubTotal = array_reduce($this->tickets, function ($carry, Category $category) {
            return $category->getPrice()->plus($carry);
        }, Money::fromCent($currency, 0));

        $donationSubTotal = $this->donation();
        return $ticketsSubTotal->plus($donationSubTotal);
    }

    public function donation()
    {
        $currency = $this->tickets[0]->getPrice()->getCurrency();

        $donationSubTotal = array_reduce(
            $this->getParticipants(),
            function ($carry, Participant $participant) {
                if ($participant->hasFundraises()) {
                    return $participant->getFundraisesAmount()->plus($carry);
                }
                return $carry;
            },
            Money::fromCent($currency, 0)
        );
        return $donationSubTotal;
    }

    public function hasDonation()
    {
        $donation = $this->donation();
        return $donation->toCent() > 0;
    }

    public function total()
    {
        return $this->subTotal();
    }
}
