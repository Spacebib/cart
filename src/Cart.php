<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 13/3/18
 * Time: 5:55 PM
 */

namespace Dilab\Cart;

use Dilab\Cart\Coupons\Coupon;
use Dilab\Cart\Donation\Donation;
use Dilab\Cart\Products\Product;
use Dilab\Cart\Traits\Serializable;
use Ramsey\Uuid\Uuid;

class Cart
{
    use Serializable;

    private $buyerEmail;

    /** @var Category[] */
    private $tickets=[];
    /**
     * @var  Coupon
     */
    private $coupon;
    /**
     * @var Product[]
     */
    private $products=[];
    /** @var Event  */
    private $event;

    /**
     * Cart constructor.
     *
     * @param $buyerEmail
     * @param Event $event
     */
    public function __construct($buyerEmail, Event $event)
    {
        $this->buyerEmail = $buyerEmail;
        $this->event = $event;
    }

    public function addTicket(Category $category, $qty)
    {
        $tickets = array_map(
            function () use ($category) {
                return $this->generateTicket($category);
            },
            range(1, $qty)
        );

        $this->tickets = array_merge($this->tickets, $tickets);

        return $this;
    }

    public function getParticipants()
    {
        $participants = array_reduce(
            $this->tickets,
            function ($carrier, Category $category) {
                $carrier = array_merge($carrier, $category->getParticipants());
                return $carrier;
            },
            []
        );

        $participants = array_values($participants);

        $participants = array_map(
            function (Participant $participant, $key) {

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

                $newParticipant->setGroupNum($participant->getGroupNum());
                $newParticipant->setAccessCode($participant->getAccessCode());
                $newParticipant->setTrackId($key);

                return $newParticipant;
            },
            $participants,
            array_keys($participants)
        );

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
     * @return Money
     */
    public function donationTotal()
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
        return $this->donationTotal()->toCent() > 0;
    }

    /**
     * @return Donation[]
     */
    public function donations()
    {
        $donations = array_reduce(
            $this->getParticipants(),
            function ($carry, Participant $participant) {
                return array_merge($carry, $participant->getFundraises());
            },
            []
        );

        return $donations;
    }

    /**
     * @return Money
     */
    public function subTotal()
    {
        $currency = $this->currency();

        $intTotal =  Money::fromCent($currency, 0);

        if (empty($this->tickets)) {
            return $intTotal;
        }

        $ticketsSubTotal = array_reduce(
            $this->tickets,
            function ($carry, Category $category) {
                return $category->getOriginalPrice()->plus($carry);
            },
            $intTotal
        );

        $donationSubTotal = $this->donationTotal();

        $productsSubTotal = $this->productsSubTotal();

        return $ticketsSubTotal
            ->plus($donationSubTotal)
            ->plus($productsSubTotal);
    }

    public function calcServiceFee()
    {
        if (! $this->shouldCalcServiceFee()) {
            return Money::fromCent($this->currency(), 0);
        }

        $serviceFee = $this->event->getServiceFee();

        $serviceFeeA = $this
            ->subtotalAfterDiscount()
            ->product($serviceFee->getPercentage()/100);

        $serviceFeeB = $serviceFee
            ->getFixed()
            ->product(count($this->getParticipants()));

        return $serviceFeeA->plus($serviceFeeB);
    }

    public function total()
    {
        $subTotalAfterDiscount = $this->subtotalAfterDiscount();

        if (! $this->shouldCalcServiceFee()) {
            return $subTotalAfterDiscount;
        }

        return $subTotalAfterDiscount->plus($this->calcServiceFee());
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

        if (!$flag) {
            $this->setCoupon(null);
        }
        
        return $flag;
    }

    public function cancelCoupon()
    {
        array_map(
            function (Category $ticket) {
                $ticket->cancelCoupon();
                return $ticket;
            },
            $this->tickets()
        );
        return true;
    }

    /**
     * get cart discount amount
     *
     * @return Money
     */
    public function getDiscount()
    {
        $currency = $this->currency();

        return array_reduce(
            $this->tickets(),
            function ($carry, Category $ticket) {
                return $ticket->getDiscount()->plus($carry);
            },
            Money::fromCent($currency, 0)
        );
    }

    public function usedCouponQuantity(): int
    {
        return array_reduce(
            $this->tickets(),
            function ($carry, Category $ticket) {
                return $carry + intval($ticket->isDiscounted());
            },
            0
        );
    }

    public function addProduct(Product $product)
    {
        $this->products[] = $product;
        return true;
    }

    /**
     * remove a product form cart
     *
     * @param int $productId
     * @param int $productVariantId
     * @return bool
     */
    public function removeProduct(int $productId, int $productVariantId)
    {
        foreach ($this->products as $i => $product) {
            if ($product->getId() == $productId
                && $product->getSelectedVariantId() == $productVariantId
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

        return array_reduce(
            $this->products,
            function ($carry, Product $product) use ($currency) {
                return $product->getSelectedVariantPrice()->plus($carry);
            },
            Money::fromCent($currency, 0)
        );
    }

    public function currency()
    {
        return $this->event->getCurrency();
    }

    private function sortTicketsByPrice()
    {
        $sortedTickets = $this->tickets();

        $sorted = usort(
            $sortedTickets,
            function (Category $ticket1, Category $ticket2) {
                if ($ticket1->getPrice()->toCent() === $ticket2->getPrice()->toCent()) {
                    return 0;
                }
                return $ticket1->getPrice()->toCent() < $ticket2->getPrice()->toCent() ? 1 : -1;
            }
        );

        if (! $sorted) {
            throw new \RuntimeException('can not sort tickets');
        }

        return $sortedTickets;
    }

    /**
     * @return Money
     */
    public function subtotalAfterDiscount(): Money
    {
        $subTotalAfterDiscount = $this->subTotal()->minus($this->getDiscount());

        return $subTotalAfterDiscount;
    }

    private function generateTicket(Category $category)
    {
        $groupNum = Uuid::uuid1()->toString();

        return new Category(
            $category->getId(),
            $category->getName(),
            $category->getPrice(),
            array_map(
                function (Participant $participant) use ($groupNum) {
                    $p = clone $participant;
                    $p->setGroupNum($groupNum);
                    $p->setAccessCode(Uuid::uuid1()->toString());
                    return $p;
                },
                $category->getParticipants()
            )
        );
    }

    private function shouldCalcServiceFee()
    {
        return $this->subtotalAfterDiscount()->toCent() !== 0;
    }
}
