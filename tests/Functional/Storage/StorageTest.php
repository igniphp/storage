<?php declare(strict_types=1);

namespace Igni\Tests\Functional\Storage;

use Igni\Storage\EntityManager;
use Igni\Storage\Exception\StorageException;
use Igni\Storage\Storage;
use Igni\Tests\Fixtures\Artist\ArtistEntity;
use Igni\Tests\Fixtures\Playlist\PlaylistEntity;
use PHPUnit\Framework\TestCase;

final class StorageTest extends TestCase
{
    use StorageTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupStorage();
        $this->loadRepositories();
    }

    public function testCanInstantiate(): void
    {
        $entityStorage = new Storage(new EntityManager());

        self::assertInstanceOf(Storage::class, $entityStorage);
    }

    public function testGet(): void
    {
        $artist = $this->unitOfWork->get(ArtistEntity::class, 1);

        self::assertInstanceOf(ArtistEntity::class, $artist);
    }

    public function testPersistNewEntity(): void
    {
        $count = $this->queryArtistsCount();
        $artist = new ArtistEntity("New Test Artist");
        $this->unitOfWork->persist($artist);

        self::assertSame($count, $this->queryArtistsCount());
    }

    public function testPersistAndCommitNewEntity(): void
    {
        $count = $this->queryArtistsCount();
        $artist = new ArtistEntity("New Test Artist");
        $this->unitOfWork->persist($artist, $artist);

        self::assertSame($count, $this->queryArtistsCount());
        $this->unitOfWork->commit();
        self::assertSame($count + 1, $this->queryArtistsCount());
    }

    public function testPersistModifiedEntity(): void
    {
        /** @var ArtistEntity $artist */
        $artist = $this->unitOfWork->get(ArtistEntity::class, 1);
        $artist->changeName('John Bohn Ohn');
        $this->unitOfWork->persist($artist);

        $this->unitOfWork->commit();

        $artist = $this->executeSql(
            'SELECT artists.Name FROM artists WHERE ArtistId = :id',
            ['id' => 1]
        )->fetch();

        self::assertSame('John Bohn Ohn', $artist['Name']);
    }

    public function testRemoveEntity(): void
    {
        /** @var ArtistEntity $artist */
        $artist = $this->unitOfWork->get(ArtistEntity::class, 1);
        $count = $this->queryArtistsCount();
        $this->unitOfWork->remove($artist);
        self::assertSame($count, $this->queryArtistsCount());
        $this->unitOfWork->commit();
        self::assertSame($count - 1, $this->queryArtistsCount());
    }

    public function testRollback(): void
    {
        $count = $this->queryArtistsCount();
        /** @var ArtistEntity $artist */
        $artist = $this->unitOfWork->get(ArtistEntity::class, 1);

        $artist->changeName('John Bohn Ohn');
        $this->unitOfWork->persist($artist);

        $artist2 = $this->unitOfWork->get(ArtistEntity::class, 2);
        $this->unitOfWork->remove($artist2);

        $this->unitOfWork->rollback();
        $this->unitOfWork->persist();

        $artistData = $this->executeSql(
            'SELECT artists.Name FROM artists WHERE ArtistId = :id',
            ['id' => 1]
        )->fetch();
        self::assertNotSame('John Bohn Ohn', $artistData['Name']);
        self::assertSame($count, $this->queryArtistsCount());

    }

    public function testAttachThanDetach(): void
    {
        $artist = new ArtistEntity("New Test Artist");
        $this->unitOfWork->attach($artist);

        self::assertTrue($this->entityManager->contains($artist));
        self::assertTrue($this->unitOfWork->contains($artist));

        $this->unitOfWork->detach($artist);

        self::assertFalse($this->entityManager->contains($artist));
        self::assertFalse($this->unitOfWork->contains($artist));
    }

    public function testGetEntityManager(): void
    {
        self::assertInstanceOf(EntityManager::class, $this->unitOfWork->getEntityManager());

        $storage = new Storage();
        self::assertInstanceOf(EntityManager::class, $storage->getEntityManager());
    }

    public function testWorkingWithRepositories()
    {
        $storage = new Storage();
        $playlistRepository = $this->createPlaylistRepository();
        $storage->addRepository($playlistRepository);
        self::assertTrue($storage->hasRepository(PlaylistEntity::class));
        self::assertSame($playlistRepository, $storage->getRepository(PlaylistEntity::class));
    }

    public function testPersistOnDetachedEntity(): void
    {
        $this->expectException(StorageException::class);
        $storage = new Storage();
        $artist = new ArtistEntity("New Test Artist");
        $storage->remove($artist);
        $storage->persist($artist);
    }

    private function queryArtistsCount(): int
    {
        return (int) $this->executeSql('SELECT count(ArtistId) as count FROM artists')->fetchColumn(0);
    }
}
