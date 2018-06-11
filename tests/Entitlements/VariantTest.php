<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/5/16
 * Time: 下午5:51
 */

namespace Dilab\Cart\Test\Entitlements;

use Dilab\Cart\Entitlements\Variant;
use Dilab\Cart\Enum\VariantStatus;
use PHPUnit\Framework\TestCase;

class VariantTest extends TestCase
{
    public function test_has_stock()
    {
        // has stock
        $variant = new Variant(1, 't', VariantStatus::ACTIVE, 0, false);

        $this->assertFalse($variant->hasStock());

        // does not has stock
        $variant = new Variant(1, 't', VariantStatus::ACTIVE, 2, false);

        $this->assertTrue($variant->hasStock());
    }

    public function test_is_available()
    {
        // available: has stock and status is active
        $variant = new Variant(1, 't', VariantStatus::ACTIVE, 0, false);
        $this->assertFalse($variant->isAvailable());

        $variant = new Variant(1, 't', VariantStatus::INACTIVE, 2, false);
        $this->assertFalse($variant->isAvailable());

        $variant = new Variant(1, 't', VariantStatus::ACTIVE, 2, false);
        $this->assertTrue($variant->isAvailable());
    }
}
