<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/5/16
 * Time: 下午5:51
 */

namespace Dilab\Cart\Test\Entitlements;

use Dilab\Cart\Entitlements\Variant;
use PHPUnit\Framework\TestCase;

class VariantTest extends TestCase
{
    public function test_has_stock()
    {
        $variant = new Variant(1, 't', 1, 0, false);

        $this->assertFalse($variant->hasStock());

        $variant = new Variant(1, 't', 1, 2, false);

        $this->assertTrue($variant->hasStock());
    }

    public function test_is_available()
    {
        $variant = new Variant(1, 't', 1, 0, false);

        $this->assertFalse($variant->isAvailable());

        $variant = new Variant(1, 't', 0, 2, false);

        $this->assertFalse($variant->isAvailable());

        $variant = new Variant(1, 't', 1, 2, false);

        $this->assertTrue($variant->isAvailable());
    }
}
