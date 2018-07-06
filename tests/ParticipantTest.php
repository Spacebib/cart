<?php
/**
 * Created by PhpStorm.
 * User: ykw
 * Date: 2018/7/6
 * Time: 上午9:58
 */

namespace Dilab\Cart\Test;

use Dilab\Cart\Participant;
use Dilab\Cart\Test\Factory\EventFactory;
use PHPUnit\Framework\TestCase;

class ParticipantTest extends TestCase
{
    public function test_from_array()
    {
        $array = EventFactory::data()['categories'][0]['participants'][0];

        $participant = Participant::fromArray($array, 1, 'HKD');

        $this->assertInstanceOf(Participant::class, $participant);

        return $participant;
    }

    /**
     * @depends test_from_array
     */
    public function test_to_entry_array(Participant $participant)
    {
        $participant->setGroupNum('111');
        $participant->setAccessCode('fgfgf');

        $array = $participant->toEntryArray();
        $this->assertArrayHasKey('fields', $array);
        $this->assertArrayHasKey('entitlements', $array);
        $this->assertArrayHasKey('fundraises', $array);
        $this->assertArrayHasKey('custom_fields', $array);

        $this->assertArrayHasKey('email', $array['fields']);
        $this->assertArrayHasKey('participant_id', $array['fields']);
        $this->assertArrayHasKey('access_code', $array['fields']);
        $this->assertArrayHasKey('grouping_num', $array['fields']);
        $this->assertArrayNotHasKey('address_sg_standard', $array['fields']);
    }
}
