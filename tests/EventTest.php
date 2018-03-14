<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 13/3/18
 * Time: 6:02 PM
 */

namespace Dilab\Cart\Test;

use Dilab\Cart\Category;
use Dilab\Cart\Event;
use Dilab\Cart\Participant;
use Dilab\Cart\Test\Factory\EventFactory;

class EventTest extends \PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        $this->assertInstanceOf(Event::class, EventFactory::create());
    }

    public function testGetParticipants()
    {
        $event = EventFactory::create();

        $this->assertCount(4, $event->getParticipants());

        $participantTrackIds = array_map(function (Participant $participant) {

            return $participant->getTrackId();

        }, $event->getParticipants());

        $expected = [0, 1, 2, 3];

        $this->assertSame($expected, $participantTrackIds);
    }

    public function testGetCategoryById()
    {
        $event = EventFactory::create();
        $result = $event->getCategoryById(1);
        $this->assertInstanceOf(Category::class, $result);

        $this->setExpectedException(\LogicException::class);
        $result = $event->getCategoryById(100000);
    }
}
