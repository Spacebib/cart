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
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    public function testInit()
    {
        $this->assertInstanceOf(Event::class, EventFactory::create());
    }

    public function testGetCategoryById()
    {
        $event = EventFactory::create();
        $result = $event->getCategoryById(1);
        $this->assertInstanceOf(Category::class, $result);

        $this->expectException(\LogicException::class);
        $event->getCategoryById(100000);
    }
}
