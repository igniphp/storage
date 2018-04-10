<?php declare(strict_types=1);

namespace IgniTestFunctional\Storage\Hydration\HydratorGenerator;

use Igni\Storage\Hydration\HydratorGenerator\GeneratedHydrator;
use Igni\Utils\TestCase;
use IgniTest\Fixtures\Album\AlbumEntity;
use IgniTest\Fixtures\Artist\ArtistEntity;
use IgniTest\Fixtures\Track\TrackEntity;

class GeneratedHydratorTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $instance = new GeneratedHydrator(TrackEntity::class);

        self::assertInstanceOf(GeneratedHydrator::class, $instance);
    }

    public function testCompile(): void
    {
        $instance = new GeneratedHydrator(TrackEntity::class);
        $instance->addProperty('name', 'string', ['field' => 'Name']);
        $instance->addProperty('album', 'reference', ['class' => AlbumEntity::class, 'field' => 'AlbumId']);
        $instance->addProperty('size', 'integer', ['field' => 'Size']);
        $instance->addProperty('unitPrice', 'float', ['field' => 'UnitPrice']);
        $instance->addProperty('artist', 'embed', ['class' => ArtistEntity::class]);

        $result = $instance->compile();

        $a = 1;
    }
}
