<?php declare(strict_types=1);

namespace IgniTestFunctional\Storage\Hydration;

use Igni\Storage\EntityManager;
use Igni\Storage\Hydration\HydratorFactory;
use Igni\Storage\Hydration\ObjectHydrator;
use Igni\Storage\Hydration\HydratorAutoGenerate;
use Igni\Storage\Mapping\ImmutableCollection;
use Igni\Storage\Mapping\MetaData\EntityMetaData;
use Igni\Storage\Mapping\MetaData\PropertyMetaData;
use Igni\Storage\Mapping\Strategy\Date;
use Igni\Storage\Mapping\Strategy\FloatNumber;
use Igni\Storage\Mapping\Strategy\Id;
use Igni\Storage\Mapping\Strategy\Reference;
use Igni\Storage\Mapping\Strategy\Text;
use Igni\Storage\Repository;
use Igni\Utils\TestCase;
use IgniTest\Fixtures\Album\AlbumEntity;
use IgniTest\Fixtures\Artist\ArtistEntity;
use IgniTest\Fixtures\Playlist\PlaylistDetails;
use IgniTest\Fixtures\Playlist\PlaylistDetailsHydrator;
use IgniTest\Fixtures\Track\TrackEntity;
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
        $metaData = $this->provideAlbumMetaData();
        $trackRepository = self::mock(Repository::class);
        $trackRepository->shouldReceive('findByAlbum')
            ->andReturn(self::mock(ImmutableCollection::class));

        $entityManager = self::mock(EntityManager::class);
        $entityManager->shouldReceive('attach');
        $entityManager
            ->shouldReceive('getHydratorNamespace')
            ->andReturn('');

        $entityManager
            ->shouldReceive('getHydratorDir')
            ->andReturn(__DIR__ . '/../../../tmp/');

        $entityManager
            ->shouldReceive('get')
            ->withArgs([ArtistEntity::class, 12])
            ->andReturn(self::mock(ArtistEntity::class));

        $entityManager
            ->shouldReceive('getMetaData')
            ->andReturn($metaData);

        $entityManager
            ->shouldReceive('getRepository')
            ->withArgs([TrackEntity::class])
            ->andReturn($trackRepository);

        $hydratorFactory = new HydratorFactory(
            $entityManager,
            HydratorAutoGenerate::ALWAYS()
        );


        $hydratorFactory->get($metaData->getClass());

        /** @var ObjectHydrator $hydrator */
        $hydrator = $metaData->getHydratorClassName();
        $hydrator = new $hydrator($entityManager);

        self::assertInstanceOf(ObjectHydrator::class, $hydrator);

        /** @var AlbumEntity $album */
        $album = $hydrator->hydrate($this->provideAlbumData());

        self::assertInstanceOf(AlbumEntity::class, $album);
        self::assertSame('Test Album', $album->getTitle());
        self::assertEquals(new \DateTime('20120101'), $album->getReleaseDate());
        self::assertInstanceOf(ArtistEntity::class, $album->getArtist());
        self::assertSame(1, $album->getId()->getValue());
    }

    public function testHydratorAsSubclass(): void
    {
        $hydratorDir = __DIR__ . '/../../../tmp';
        $metaData = $this->providePlayListDetailsMetaData();

        $trackRepository = self::mock(TrackRepository::class);
        $trackRepository
            ->shouldReceive('getMultiple')
            ->andReturn(self::mock(ImmutableCollection::class));

        $entityManager = self::mock(EntityManager::class);
        $entityManager
            ->shouldReceive('getHydratorNamespace')
            ->andReturn('');
        $entityManager
            ->shouldReceive('getHydratorDir')
            ->andReturn($hydratorDir);

        $entityManager
            ->shouldReceive('getRepository')
            ->andReturn($trackRepository);
        $entityManager
            ->shouldReceive('getMetaData')
            ->andReturn($metaData);

        $hydratorFactory = new HydratorFactory(
            $entityManager,
            HydratorAutoGenerate::ALWAYS()
        );

        $playlistDetailsHydrator = $hydratorFactory->get($metaData->getClass());

        $playlistDetails = $playlistDetailsHydrator->hydrate([
            'rating' => '4.2',
            'songs' => [1, 2, 3, 8]
        ]);

        self::assertInstanceOf(PlaylistDetails::class, $playlistDetails);
    }

    public function testHydratorsFromCustomNamespace(): void
    {
        $hydratorDir = __DIR__ . '/../../../tmp';
        $metaData = $this->providePlayListDetailsMetaData();

        $trackRepository = self::mock(TrackRepository::class);
        $trackRepository
            ->shouldReceive('getMultiple')
            ->andReturn(self::mock(ImmutableCollection::class));

        $entityManager = self::mock(EntityManager::class);
        $entityManager
            ->shouldReceive('getHydratorNamespace')
            ->andReturn('TestNamespace');
        $entityManager
            ->shouldReceive('getHydratorDir')
            ->andReturn($hydratorDir);

        $entityManager
            ->shouldReceive('getRepository')
            ->andReturn($trackRepository);
        $entityManager
            ->shouldReceive('getMetaData')
            ->andReturn($metaData);

        $hydratorFactory = new HydratorFactory(
            $entityManager,
            HydratorAutoGenerate::ALWAYS()
        );

        $playlistDetailsHydrator = $hydratorFactory->get($metaData->getClass());

        $playlistDetails = $playlistDetailsHydrator->hydrate([
            'rating' => '4.2',
            'songs' => [1, 2, 3, 8]
        ]);

        self::assertInstanceOf(PlaylistDetails::class, $playlistDetails);
    }

    private function providePlayListDetailsMetaData(): EntityMetaData
    {
        $metaData = new EntityMetaData(PlaylistDetails::class);
        $metaData->setCustomHydratorClass(PlaylistDetailsHydrator::class);

        $rating = new PropertyMetaData('rating', FloatNumber::class);
        $metaData->addProperty($rating);

        return $metaData;
    }

    private function provideAlbumMetaData(): EntityMetaData
    {
        $metaData = new EntityMetaData(AlbumEntity::class);

        $id = new PropertyMetaData('id', Id::class);
        $id->setFieldName('AlbumId');
        $metaData->addProperty($id);

        $artist = new PropertyMetaData('artist', Reference::class);
        $artist->setFieldName('ArtistId');
        $artist->setAttributes(['target' => ArtistEntity::class]);
        $metaData->addProperty($artist);

        $title = new PropertyMetaData('title', Text::class);
        $title->setFieldName('Title');
        $metaData->addProperty($title);

        $releaseDate = new PropertyMetaData('releaseDate', Date::class);
        $releaseDate->setFieldName('ReleaseDate');
        $metaData->addProperty($releaseDate);

        return $metaData;
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
