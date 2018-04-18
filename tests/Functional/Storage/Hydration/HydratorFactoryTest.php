<?php declare(strict_types=1);

namespace IgniTestFunctional\Storage\Hydration;

use Igni\Storage\EntityManager;
use Igni\Storage\Hydration\HydratorFactory;
use Igni\Storage\Hydration\ObjectHydrator;
use Igni\Storage\Hydration\Strategy\HydratorAutoGenerate;
use Igni\Storage\Mapping\EntityMetaData;
use Igni\Storage\Mapping\Strategy\Date;
use Igni\Storage\Mapping\Strategy\Id;
use Igni\Storage\Mapping\Strategy\Reference;
use Igni\Storage\Mapping\Strategy\Text;
use Igni\Utils\TestCase;
use IgniTest\Fixtures\Album\AlbumEntity;
use IgniTest\Fixtures\Artist\ArtistEntity;

class HydratorFactoryTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $hydratorFactory = new HydratorFactory(
            self::mock(EntityManager::class),
            HydratorAutoGenerate::ALWAYS()
        );

        self::assertInstanceOf(HydratorFactory::class, $hydratorFactory);
    }

    public function testHydratorCreation(): void
    {
        $entityManager = self::mock(EntityManager::class);
        $entityManager
            ->shouldReceive('getHydratorNamespace')
            ->andReturn('\\');

        $entityManager
            ->shouldReceive('get')
            ->withArgs([ArtistEntity::class, 12])
            ->andReturn(self::mock(ArtistEntity::class));

        $hydratorFactory = new HydratorFactory(
            $entityManager,
            HydratorAutoGenerate::ALWAYS()
        );

        $metaData = new EntityMetaData(AlbumEntity::class, 'albums', [
            'id' => [
                'field' => 'AlbumId',
                'type' => Id::class,
            ],
            'artist' => [
                'field' => 'ArtistId',
                'type' => Reference::class,
                'attributes' => [
                    'class' => ArtistEntity::class,
                ]
            ],
            'title' => [
                'field' => 'Title',
                'type' => Text::class,
            ],
            'releaseDate' => [
                'field' => 'ReleaseDate',
                'type' => Date::class,
                'attributes' => [
                    'format' => 'Ymd',
                ]
            ],
        ]);

        $hydratorFactory->create($metaData, $load = true);

        /** @var ObjectHydrator $hydrator */
        $hydrator = $metaData->getHydratorClassName();
        $hydrator = new $hydrator($entityManager);

        self::assertInstanceOf(ObjectHydrator::class, $hydrator);

        /** @var AlbumEntity $album */
        $album = $hydrator->hydrate($metaData->createInstance(), [
            'ReleaseDate' => '20120101',
            'Title' => 'Test Album',
            'ArtistId' => 12,
            'AlbumId' => 1,
        ]);

        self::assertInstanceOf(AlbumEntity::class, $album);
        self::assertSame('Test Album', $album->getTitle());
        self::assertEquals(new \DateTime('20120101'), $album->getReleaseDate());
        self::assertInstanceOf(ArtistEntity::class, $album->getArtist());
        self::assertSame(1, $album->getId()->getValue());
    }
}
