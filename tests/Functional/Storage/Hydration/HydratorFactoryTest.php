<?php declare(strict_types=1);

namespace IgniTestFunctional\Storage\Hydration;

use Igni\Storage\EntityManager;
use Igni\Storage\Hydration\HydratorFactory;
use Igni\Storage\Hydration\ObjectHydrator;
use Igni\Storage\Hydration\HydratorAutoGenerate;
use Igni\Storage\Mapping\ImmutableCollection;
use Igni\Storage\Mapping\MetaData\EntityMetaData;
use Igni\Storage\Mapping\Strategy\Date;
use Igni\Storage\Mapping\Strategy\Delegate;
use Igni\Storage\Mapping\Strategy\FloatNumber;
use Igni\Storage\Mapping\Strategy\Id;
use Igni\Storage\Mapping\Strategy\Reference;
use Igni\Storage\Mapping\Strategy\Text;
use Igni\Utils\TestCase;
use IgniTest\Fixtures\Album\AlbumEntity;
use IgniTest\Fixtures\Artist\ArtistEntity;
use IgniTest\Fixtures\Playlist\PlaylistDetails;
use IgniTest\Fixtures\Playlist\PlaylistDetailsHydrator;
use IgniTest\Fixtures\Track\TrackRepository;

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

        $metaData = $this->provideAlbumMetaData();
        $hydratorFactory->create($metaData, $load = true);

        /** @var ObjectHydrator $hydrator */
        $hydrator = $metaData->getHydratorClassName();
        $hydrator = new $hydrator($entityManager);

        self::assertInstanceOf(ObjectHydrator::class, $hydrator);

        /** @var AlbumEntity $album */
        $album = $hydrator->hydrate($metaData->createInstance(), $this->provideAlbumData());

        self::assertInstanceOf(AlbumEntity::class, $album);
        self::assertSame('Test Album', $album->getTitle());
        self::assertEquals(new \DateTime('20120101'), $album->getReleaseDate());
        self::assertInstanceOf(ArtistEntity::class, $album->getArtist());
        self::assertSame(1, $album->getId()->getValue());
    }

    public function testGetHydrator(): void
    {
        $hydratorDir = __DIR__ . '/../../../tmp';

        $entityManager = self::mock(EntityManager::class);
        $entityManager
            ->shouldReceive('getHydratorNamespace')
            ->andReturn('\\');

        $entityManager
            ->shouldReceive('get')
            ->withArgs([ArtistEntity::class, 12])
            ->andReturn(self::mock(ArtistEntity::class));

        $entityManager
            ->shouldReceive('getHydratorDir')
            ->andReturn($hydratorDir);

        $hydratorFactory = new HydratorFactory(
            $entityManager,
            HydratorAutoGenerate::ALWAYS()
        );

        $metaData = $this->provideAlbumMetaData();
        $hydrator = $hydratorFactory->get($metaData);
        self::assertInstanceOf(ObjectHydrator::class, $hydrator);

        /** @var AlbumEntity $album */
        $album = $hydrator->hydrate($metaData->createInstance(), $this->provideAlbumData());

        self::assertInstanceOf(AlbumEntity::class, $album);
        self::assertSame('Test Album', $album->getTitle());
        self::assertEquals(new \DateTime('20120101'), $album->getReleaseDate());
        self::assertInstanceOf(ArtistEntity::class, $album->getArtist());
        self::assertSame(1, $album->getId()->getValue());

    }

    public function testHydratorAsSubclass(): void
    {
        $hydratorDir = __DIR__ . '/../../../tmp';

        $trackRepository = self::mock(TrackRepository::class);
        $trackRepository
            ->shouldReceive('getMultiple')
            ->andReturn(self::mock(ImmutableCollection::class));

        $entityManager = self::mock(EntityManager::class);
        $entityManager
            ->shouldReceive('getHydratorNamespace')
            ->andReturn('\\');
        $entityManager
            ->shouldReceive('getHydratorDir')
            ->andReturn($hydratorDir);

        $entityManager
            ->shouldReceive('getRepository')
            ->andReturn($trackRepository);

        $hydratorFactory = new HydratorFactory(
            $entityManager,
            HydratorAutoGenerate::ALWAYS()
        );

        $metaData = $this->providePlayListDetailsMetaData();

        $playlistDetailsHydrator = $hydratorFactory->get($metaData);

        $playlistDetailsHydrator->hydrate($metaData->createInstance(), [
            'rating' => '4.2',
            'tracks' => [1, 2, 3, 8]
        ]);

    }

    private function providePlayListDetailsMetaData(): EntityMetaData
    {
        $metaData = new EntityMetaData(PlaylistDetails::class, [
            'rating' => [
                'field' => 'rating',
                'type' => FloatNumber::class,
            ],
            'tracks' => [
                'field' => 'tracks',
                'type' => Delegate::class,
            ],
        ]);
        $metaData->setParentHydratorClass(PlaylistDetailsHydrator::class);

        return $metaData;
    }

    private function provideAlbumMetaData(): EntityMetaData
    {
        return new EntityMetaData(AlbumEntity::class, [
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
    }

    private function provideAlbumData(): array
    {
        return [
            'ReleaseDate' => '20120101',
            'Title' => 'Test Album',
            'ArtistId' => 12,
            'AlbumId' => 1,
        ];
    }
}
