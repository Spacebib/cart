<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 13/3/18
 * Time: 5:55 PM
 */

namespace Dilab\Cart;

use Dilab\Cart\Coupons\Coupon;
use Dilab\Cart\Products\Product;
use Dilab\Cart\Traits\Serializable;

class Cart
{
    use Serializable;

    private $buyerEmail;

    private $tickets;
    /** @var  Coupon */
    private $coupon;
    /** @var Product[] */
    private $products=[];

    private $event;

    /**
     * Cart constructor.
     * @param $buyerEmail
     * @param Event $event
     */
    public function __construct($buyerEmail, Event $event)
    {
        $this->buyerEmail = $buyerEmail;
        $this->event = $event;
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
                $participant->getFundraises(),
                clone $participant->getCustomFields()
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

    public function donation()
    {
        $currency = $this->currency();

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

    /**
     * @return null | Money
     */
    public function subTotal()
    {
        if (empty($this->tickets)) {
            return null;
        }

        $currency = $this->currency();

        $ticketsSubTotal = array_reduce($this->tickets, function ($carry, Category $category) {
            return $category->getOriginalPrice()->plus($carry);
        }, Money::fromCent($currency, 0));

        $donationSubTotal = $this->donation();

        $productsSubTotal = $this->productsSubTotal();

        return $ticketsSubTotal->plus($donationSubTotal)->plus($productsSubTotal);
    }

    public function calcServiceFee()
    {
        $subTotalAfterDiscount = $this->subTotal()->minus($this->getDiscount());

        $serviceFee = $this->event->getServiceFee();

        $serviceFeeA = $subTotalAfterDiscount->product($serviceFee->getPercentage()/100);

        $serviceFeeB = $serviceFee->getFixed()->product(count($this->getParticipants()));

        return $serviceFeeA->plus($serviceFeeB);
    }

    public function total()
    {
        $subTotal = $this->subTotal();

        if (!$subTotal) {
            return $subTotal;
        }

        return $subTotal->plus($this->calcServiceFee())->minus($this->getDiscount());
    }

    public function setCoupon($coupon)
    {
        $this->coupon = $coupon;
        return $this;
    }

    public function getCoupon()
    {
        return $this->coupon;
    }

    public function applyCoupon()
    {
        if (! $this->coupon instanceof Coupon) {
            throw new \RuntimeException('please call setCoupon first');
        }
        // 将票按价格降序排列，coupon 按顺序给每张票都尝试着用一次
        $sortedTickets = $this->sortTicketsByPrice();

        $flag = false;

        foreach ($sortedTickets as $ticket) {
            if ($ticket->applyCoupon($this->coupon)) {
                $flag = true;
            }
        }

        return $flag;
    }

    public function cancelCoupon()
    {
        array_map(function (Category $ticket) {
            $ticket->cancelCoupon();
            return $ticket;
        }, $this->tickets());
        return true;
    }

    public function getDiscount()
    {
        $currency = $this->currency();

        return array_reduce($this->tickets(), function ($carry, Category $ticket) {
            return $ticket->getDiscount()->plus($carry);
        }, Money::fromCent($currency, 0));
    }

    public function usedCouponQuantity(): int
    {
        return array_reduce($this->tickets(), function ($carry, Category $ticket) {
            return $carry + intval($ticket->isDiscounted());
        }, 0);
    }

    public function addProduct(Product $product)
    {
        $this->products[] = $product;
        return true;
    }

    public function removeProduct($productId, $productVariantId)
    {
        foreach ($this->products as $i => $product) {
            if ($product->getId() == $productId &&
                $product->getSelectedVariantId() == $productVariantId
            ) {
                unset($this->products[$i]);
                break;
            }
        }

        return true;
    }

    public function getProducts()
    {
        return $this->products;
    }

    public function countProducts()
    {
        return count($this->products);
    }

    public function productsSubTotal()
    {
        $currency = $this->currency();

        return array_reduce($this->products, function ($carry, Product $product) use ($currency) {
            return $product->getSelectedVariantPrice()->plus($carry);
        }, Money::fromCent($currency, 0));
    }

    public function currency()
    {
        return $this->event->getCurrency();
    }

    private function sortTicketsByPrice()
    {
        $sortedTickets = $this->tickets();

        $sorted = usort($sortedTickets, function (Category $ticket1, Category $ticket2) {
            if ($ticket1->getPrice()->toCent() === $ticket2->getPrice()->toCent()) {
                return 0;
            }
            return $ticket1->getPrice()->toCent() < $ticket2->getPrice()->toCent() ? 1 : -1;
        });

        if (! $sorted) {
            throw new \RuntimeException('can not sort tickets');
        }

        return $sortedTickets;
    }
}
