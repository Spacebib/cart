<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/26
 * Time: 下午4:03
 */

namespace Dilab\Cart\Test\Entitlements;

use Dilab\Cart\Entitlements\Entitlement;
use Dilab\Cart\Entitlements\Variant;
use PHPUnit\Framework\TestCase;

class EntitlementTest extends TestCase
{
    /**
     * @var Entitlement
     */
    public $entitlement;

    public function setUp()
    {
        parent::setUp();
        $this->entitlement = new Entitlement(
            1,
            'shorts',
            'run xxx',
            '',
            '',
            '',
            [
                new Variant(1, 'size:s', 1, 10),
                new Variant(2, 'size:m', 0, 10),
                new Variant(2, 'size:m', 1, 0)
            ]
        );
    }

    public function test_select_variant()
    {
        $selectedId = $this->entitlement->getSelectedVariantId();
        $this->assertEquals('', $selectedId);

        $this->entitlement->setSelectedVariantId(0);
        $selectedId = $this->entitlement->getSelectedVariantId();
        $this->assertEquals('', $selectedId);

        $this->entitlement->setSelectedVariantId(1);
        $selectedId = $this->entitlement->getSelectedVariantId();
        $this->assertEquals(1, $selectedId);

        $this->entitlement->setSelectedVariantId('');
        $selectedId = $this->entitlement->getSelectedVariantId();
        $this->assertEquals('', $selectedId);
    }

    public function test_get_variants_has_stock()
    {
        $this->assertCount(3, $this->entitlement->getVariants());
        $this->assertCount(2, $this->entitlement->getVariantsHasStock());
    }

    public function test_get_variants_is_available()
    {
        $this->assertCount(3, $this->entitlement->getVariants());
        $this->assertCount(1, $this->entitlement->getVariantsAvailable());
    }
}
