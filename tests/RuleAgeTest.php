<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/21
 * Time: 上午11:39
 */

namespace Dilab\Cart\Test;

use Dilab\Cart\Rules\RuleAge;
use Dilab\Cart\Test\Factory\FormDataFactory;

class RuleAgeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RuleAge
     */
    private $rule;

    public function setUp()
    {
        parent::setUp();
        $this->rule = new RuleAge('>=18');

    }
    public function testAge()
    {
        $data = FormDataFactory::correctData();

        $data['dob'] = ['day'=>1, 'month'=>2, 'year'=>2017];
        $this->assertFalse($this->rule->valid($data));
        $this->assertContains('years old & above', $this->rule->errors()['dob']);

        $data['dob'] = ['day'=>1, 'month'=>2, 'year'=>1995];
        $this->assertTrue($this->rule->valid($data));
        $this->assertEmpty($this->rule->errors());
    }
}
