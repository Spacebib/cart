<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/3/21
 * Time: 下午5:12
 */

namespace Dilab\Cart\Test\Rules;

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

    public function test_email_confirmation()
    {
        $data = FormDataFactory::correctData();
        $data['email_confirmation'] = 'ss';
        $this->assertFalse($this->rule->valid($data));
        $this->assertEquals(
            ['email_confirmation'=>'Email addresses do not match'],
            $this->rule->errors()
        );
    }

    public function test_email_regex()
    {
        $data = FormDataFactory::correctData();

        $this->assertTrue($this->rule->valid($data));
        $this->assertEmpty($this->rule->errors());

        $data['email'] = 'xx';
        $this->assertFalse($this->rule->valid($data));
        $this->assertEquals(['email'=>'Invalid email address format'], $this->rule->errors());
    }

    public function test_191()
    {
        // https://github.com/Spacebib/starpodium/issues/191
        //email format should be valid when there is a "."
        $data = FormDataFactory::correctData();

        $data['email'] = 'celine.os@gamil.com';
        $data['email_confirmation'] = 'celine.os@gamil.com';

        $this->assertTrue($this->rule->valid($data));
        $this->assertEmpty($this->rule->errors());
    }
}
