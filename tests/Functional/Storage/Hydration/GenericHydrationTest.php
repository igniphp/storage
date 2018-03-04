<?php declare(strict_types=1);

namespace IgniTestFunctional\Storage\Hydration;

use Igni\Storage\Hydration\Hydrator;
use Igni\Utils\TestCase;
use IgniTest\Fixtures\Album\AlbumEntity;
use IgniTest\Fixtures\Genre\GenreEntity;
use IgniTest\Fixtures\Track\TrackEntity;
use IgniTest\Fixtures\Track\TrackSchema;
use IgniTest\Functional\Storage\StorageTrait;

class GenericHydrationTest extends TestCase
{
    use StorageTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupStorage();
        $this->loadRepositories();
    }

    public function testHydration(): void
    {
        /** @var TrackEntity $track */
        $track = $this->entityManager->get(TrackEntity::class, 1);

        self::assertInstanceOf(TrackEntity::class, $track);
        self::assertSame(1, $track->getId());
        self::assertSame(0.99, $track->getPrice());
        self::assertInstanceOf(AlbumEntity::class, $track->getAlbum());
        self::assertInstanceOf(GenreEntity::class, $track->getGenre());

    }

    public function testExtraction(): void
    {
        $album = $this->entityManager->get(AlbumEntity::class, 1);
        $track = new TrackEntity(69696969, 'Test Name', 'Test Composer', $album);
        $hydrator = new Hydrator($this->entityManager, TrackSchema::instance());

        $data = $hydrator->extract($track);

        self::assertEquals(69696969, $data['TrackId']);
        self::assertEquals('Test Name', $data['Name']);
        self::assertEquals(1, $data['AlbumId']);
        self::assertNull($data['GenreId']);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->clearStorage();
    }
}
