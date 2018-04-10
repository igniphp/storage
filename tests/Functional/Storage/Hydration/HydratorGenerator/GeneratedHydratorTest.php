<?php declare(strict_types=1);

namespace IgniTestFunctional\Storage\Hydration\HydratorGenerator;

use Igni\Storage\EntityManager;
use Igni\Storage\Hydration\HydratorGenerator\GeneratedHydrator;
use Igni\Storage\Hydration\ObjectHydrator;
use Igni\Utils\ReflectionApi;
use Igni\Utils\TestCase;
use IgniTest\Fixtures\Album\AlbumEntity;
use IgniTest\Fixtures\Track\TrackEntity;

class GeneratedHydratorTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $instance = new GeneratedHydrator(TrackEntity::class);

        self::assertInstanceOf(GeneratedHydrator::class, $instance);
    }

    public function testCompileAndLoad(): void
    {
        $entityManager = new EntityManager();
        $instance = new GeneratedHydrator(TrackEntity::class);
        $instance->addProperty('name', 'string', ['field' => 'Name']);
        $instance->addProperty('album', 'reference', ['class' => AlbumEntity::class, 'field' => 'AlbumId']);
        $instance->addProperty('size', 'integer', ['field' => 'Size']);
        $instance->addProperty('unitPrice', 'float', ['field' => 'UnitPrice']);

        $instance->compile();
        $instance->load();

        $hydratorClass = $instance->getClassName();

        /** @var ObjectHydrator $hydrator */
        $hydrator = new $hydratorClass($entityManager);
        $instance = ReflectionApi::createInstance(TrackEntity::class);
        $instance = $hydrator->hydrate($instance, [
            'Name' => 'test',
            'AlbumId' => 1,
            'Size' => 100000,
            'UnitPrice' => '12.00'
        ]);

    }
}
