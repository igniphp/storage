<?php declare(strict_types=1);

namespace IgniTest\Functional\Storage;

use Igni\Storage\EntityManager;
use Igni\Storage\EntityStorage;
use Igni\Utils\TestCase;
use IgniTest\Fixtures\Artist\ArtistEntity;

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
        $count = $this->executeSql('SELECT count(ArtistId) as count FROM artists')->fetchColumn(0);
        $artist = new ArtistEntity("New Test Artist");
        $this->unitOfWork->persist($artist);

        self::assertSame($count, $this->executeSql('SELECT count(ArtistId) as count FROM artists')->fetchColumn(0));
    }

    public function testPersistAndCommitNewEntity(): void
    {
        $count = $this->executeSql('SELECT count(ArtistId) as count FROM artists')->fetchColumn(0);
        $artist = new ArtistEntity("New Test Artist");
        $this->unitOfWork->persist($artist);
        $this->unitOfWork->commit();
        self::assertSame($count + 1, 0 + $this->executeSql('SELECT count(ArtistId) as count FROM artists')->fetchColumn(0));
    }
}
