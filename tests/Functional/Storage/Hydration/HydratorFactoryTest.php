<?php declare(strict_types=1);

namespace IgniTestFunctional\Storage\Hydration;

use Igni\Storage\Hydration\HydratorFactory;
use Igni\Utils\TestCase;
use IgniTest\Fixtures\Track\TrackEntity;

class HydratorFactoryTest extends TestCase
{
    public function testForSimpleEntity()
    {
        HydratorFactory::configure(__DIR__ . '/../../../tmp');

        $hydrator = HydratorFactory::instance()
            ->generate(TrackEntity::class);
    }
}
