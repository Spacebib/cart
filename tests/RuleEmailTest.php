<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/21
 * Time: 下午5:12
 */

namespace Dilab\Cart\Test;

use Dilab\Cart\Rules\RuleEmail;
use Dilab\Cart\Test\Factory\FormDataFactory;
use PHPUnit\Framework\TestCase;

class RuleEmailTest extends TestCase
{
    /**
     * @var RuleEmail
     */
    private $rule;
    public function setUp()
    {
        parent::setUp();
        $this->rule = new RuleEmail();

    }
    public function testGender()
    {
        $data = FormDataFactory::correctData();

        $data['email'] = 'xx';
        $this->assertFalse($this->rule->valid($data));
        $this->assertEquals(['email'=>'Invalid email address format'], $this->rule->errors());

        $data = FormDataFactory::correctData();
        $data['email_confirmation'] = 'ss';
        $this->assertFalse($this->rule->valid($data));
        $this->assertEquals(
            ['email_confirmation'=>'Email addresses do not match'],
            $this->rule->errors()
        );

        $data = FormDataFactory::correctData();
        $this->assertTrue($this->rule->valid($data));
        $this->assertEmpty($this->rule->errors());
    }
}
