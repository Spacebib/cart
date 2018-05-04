<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/26
 * Time: 下午4:03
 */

namespace Dilab\Cart\Test;

use Dilab\Cart\Entitlement;
use Dilab\Cart\Variant;
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
                new Variant(1, 'size:s', 1),
                new Variant(2, 'size:m', 1)
            ]
        );
    }

    public function testSelected()
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
}
