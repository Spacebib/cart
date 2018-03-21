<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/21
 * Time: 下午12:02
 */

namespace Dilab\Cart\Test;

use Dilab\Cart\Rules\RuleGender;
use Dilab\Cart\Test\Factory\FormDataFactory;

class RuleGenderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RuleGender
     */
    private $rule;

    public function setUp()
    {
        parent::setUp();
        $this->rule = new RuleGender('male');

    }
    public function testAge()
    {
        $data = FormDataFactory::correctData();

        $data['gender'] = 'female';
        $this->assertFalse($this->rule->valid($data));
        $this->assertContains('Male Only', $this->rule->errors()['gender']);

        $data['gender'] = 'male';
        $this->assertTrue($this->rule->valid($data));
        $this->assertEmpty($this->rule->errors());
    }
}
