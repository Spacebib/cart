<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/21
 * Time: 上午11:39
 */

namespace Dilab\Cart\Test\Rules;

use Dilab\Cart\Rules\RuleAge;
use Dilab\Cart\Test\Factory\FormDataFactory;
use PHPUnit\Framework\TestCase;

class RuleAgeTest extends TestCase
{
    /**
     * @var RuleAge
     */
    private $rule;

    public function setUp()
    {
        parent::setUp();
    }

    public function test_pattern_condition_works()
    {
        $this->rule = new RuleAge('>=18');

        $data = FormDataFactory::correctData();

        $data['dob'] = ['day'=>1, 'month'=>2, 'year'=>2017];
        $this->assertFalse($this->rule->valid($data));
        $this->assertContains('years old & above', $this->rule->errors()['dob']);

        $data['dob'] = ['day'=>27, 'month'=>1, 'year'=>2000];
        $this->assertTrue($this->rule->valid($data));
        $this->assertEmpty($this->rule->errors());
    }

    public function test_array_condition_works()
    {
        $this->rule = new RuleAge([
            'comp' => '>=',
            'from' => 18,
            'to' => 1
        ]);

        $data = FormDataFactory::correctData();

        $data['dob'] = ['day'=>1, 'month'=>2, 'year'=>2017];
        $this->assertFalse($this->rule->valid($data));
        $this->assertContains('years old & above', $this->rule->errors()['dob']);

        $data['dob'] = ['day'=>27, 'month'=>1, 'year'=>2000];
        $this->assertTrue($this->rule->valid($data));
        $this->assertEmpty($this->rule->errors());
    }

    public function test_less_than_and_below()
    {
        $this->rule = new RuleAge([
            'comp' => '<=',
            'from' => 18,
            'to' => 1
        ]);

        $data = FormDataFactory::correctData();

        $data['dob'] = ['day'=>1, 'month'=>2, 'year'=>1999];
        $this->assertFalse($this->rule->valid($data));
        $this->assertContains('years old & below', $this->rule->errors()['dob']);

        $data['dob'] = ['day'=>27, 'month'=>1, 'year'=>2000];
        $this->assertTrue($this->rule->valid($data));
        $this->assertEmpty($this->rule->errors());
    }

    public function test_between()
    {
        $this->rule = new RuleAge([
            'comp' => '><=',
            'from' => 1,
            'to' => 18
        ]);

        $data = FormDataFactory::correctData();

        $data['dob'] = ['day'=>1, 'month'=>2, 'year'=>1999];
        $this->assertFalse($this->rule->valid($data));
        $this->assertContains(sprintf('From %s years old to %s years old', 1, 18), $this->rule->errors()['dob']);

        $data['dob'] = ['day'=>27, 'month'=>1, 'year'=>2000];
        $this->assertTrue($this->rule->valid($data));
        $this->assertEmpty($this->rule->errors());
    }
}
