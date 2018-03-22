<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/22
 * Time: 下午3:34
 */

namespace Dilab\Cart\Test;

use Dilab\Cart\Rules\RuleLength;
use Dilab\Cart\Test\Factory\FormDataFactory;

class RuleLengthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RuleLength
     */
    private $rule;

    public function setUp()
    {
        parent::setUp();

        $this->rule = new RuleLength();
    }

    public function testValid()
    {
        $data = FormDataFactory::correctData();
        $this->assertTrue($this->rule->valid($data));
        $data['nric'] = base64_encode(random_bytes(109));
        $this->assertFalse($this->rule->valid($data));

    }
}
