<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/5/17
 * Time: 上午9:15
 */

namespace Dilab\Cart\Test\Products;

use Dilab\Cart\Enum\VariantStatus;
use Dilab\Cart\Money;
use Dilab\Cart\Products\Product;
use Dilab\Cart\Products\Variant;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    /**
     * @var Product
     */
    private $product;

    public function setUp()
    {
        parent::setUp();
        $this->product = new Product(
            1,
            'product1',
            'this is a product',
            '',
            '',
            '',
            [
                new Variant(1, 'v1', 10, VariantStatus::ACTIVE, Money::fromCent('HKD', 100)),
                new Variant(2, 'v2', 10, VariantStatus::INACTIVE, Money::fromCent('HKD', 200)),
                new Variant(3, 'v3', 0, VariantStatus::ACTIVE, Money::fromCent('HKD', 300)),
            ]
        );
    }

    public function test_select_product_variant()
    {
        $selectedId = $this->product->getSelectedVariantId();
        $this->assertEquals('', $selectedId);

        $this->product->setSelectedVariantId(0);
        $selectedId = $this->product->getSelectedVariantId();
        $this->assertEquals('', $selectedId);

        $this->product->setSelectedVariantId(1);
        $selectedId = $this->product->getSelectedVariantId();
        $this->assertEquals(1, $selectedId);

        $this->product->setSelectedVariantId('');
        $selectedId = $this->product->getSelectedVariantId();
        $this->assertEquals('', $selectedId);
    }

    public function test_can_not_select_is_not_available_variant()
    {
        $this->product->setSelectedVariantId(2);
        $selectedId = $this->product->getSelectedVariantId();
        $this->assertEquals('', $selectedId);

        $this->product->setSelectedVariantId(3);
        $selectedId = $this->product->getSelectedVariantId();
        $this->assertEquals('', $selectedId);
    }

    public function test_get_selected_variant_price()
    {
        $this->product->setSelectedVariantId(1);
        $price = $this->product->getSelectedVariantPrice();
        $this->assertEquals(Money::fromCent('HKD', 100), $price);

        $this->product->setSelectedVariantId(0);
        $price = $this->product->getSelectedVariantPrice();
        $this->assertEquals(null, $price);
    }

    public function test_get_currency()
    {
        $this->assertEquals('HKD', $this->product->getCurrency());
    }

    public function test_get_variants_is_available()
    {
        $this->assertCount(3, $this->product->getVariants());
        $this->assertCount(1, $this->product->getAvailableVariants());
    }
}
