<?php declare(strict_types=1);

namespace IgniTest\Functional\Storage;

use Igni\Storage\EntityManager;
use Igni\Storage\EntityStorage;
use IgniTest\Fixtures\Artist\ArtistEntity;
use PHPUnit\Framework\TestCase;

final class EntityStorageTest extends TestCase
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
        $entityStorage = new EntityStorage(new EntityManager());

        self::assertInstanceOf(EntityStorage::class, $entityStorage);
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

    private function queryArtistsCount(): int
    {
        return (int) $this->executeSql('SELECT count(ArtistId) as count FROM artists')->fetchColumn(0);
    }
}
