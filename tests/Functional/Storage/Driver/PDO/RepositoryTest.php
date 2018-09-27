<?php declare(strict_types=1);

namespace Igni\Tests\Functional\Storage\Driver\PDO;

use Igni\Tests\Fixtures\Album\AlbumEntity;
use Igni\Tests\Fixtures\Artist\ArtistEntity;
use Igni\Tests\Functional\Storage\StorageTrait;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    use StorageTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupStorage();
        $this->loadRepositories();
    }

    public function testGet(): void
    {
        $albumRepository = $this->entityManager->getRepository(AlbumEntity::class);

        $album = $albumRepository->get(1);

        self::assertInstanceOf(AlbumEntity::class, $album);

        $album2 = $albumRepository->get(1);

        self::assertSame($album, $album2);

        $this->entityManager->clear();

        $album3 = $albumRepository->get(1);

        self::assertNotSame($album3, $album);
    }

    public function testCreate(): void
    {
        /** @var ArtistEntity $artist */
        $artist = $this->entityManager->get(ArtistEntity::class, 1);
        $album = new AlbumEntity('Test Album creation', $artist);

        $this->entityManager->create($album);

        $cursor = $this->sqliteConnection->createCursor(
            'SELECT * FROM albums WHERE AlbumId = :id',
            ['id' => $album->getId()->getValue()]
        );
        self::assertTrue($cursor->valid());
        $data = $cursor->current();
        self::assertSame(
            [
                'AlbumId' => $album->getId()->getValue(),
                'Title' => $album->getTitle(),
                'ArtistId' => $album->getArtist()->getId()->getValue(),
                'ReleaseDate' => null
            ],
            $data
        );
    }

    public function testRemove(): void
    {
        $artist = $this->entityManager->get(ArtistEntity::class, 1);

        $this->entityManager->remove($artist);

        $cursor = $this->sqliteConnection->createCursor('SELECT * FROM artists WHERE ArtistId = :id', ['id' => $artist->getId()]);
        self::assertFalse($cursor->valid());
    }

    public function testUpdate(): void
    {
        /** @var ArtistEntity $artist */
        $artist = $this->entityManager->get(ArtistEntity::class, 1);
        $artist->changeName('AC (blizzard) DC');

        $this->entityManager->update($artist);

        $cursor = $this->sqliteConnection->createCursor('SELECT * FROM artists WHERE ArtistId = :id', ['id' => $artist->getId()]);
        self::assertTrue($cursor->valid());
        $data = $cursor->current();

        self::assertSame($artist->getName(), $data['Name']);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->clearStorage();
    }
}
