<?php declare(strict_types=1);

namespace IgniTest\Functional\Storage;

use Igni\Storage\Driver\ConnectionManager;
use Igni\Storage\Driver\MongoDB\Connection as MongoDBConnection;
use Igni\Storage\Driver\MongoDB\Connection;
use Igni\Storage\Driver\MongoDB\ConnectionOptions as MongoDBOptions;
use Igni\Storage\Driver\Pdo\Connection as SqliteConnection;
use Igni\Storage\Driver\Pdo\Cursor;
use Igni\Storage\EntityManager;
use Igni\Storage\Storage;
use IgniTest\Fixtures\Album\AlbumRepository;
use IgniTest\Fixtures\Artist\ArtistRepository;
use IgniTest\Fixtures\Genre\GenreRepository;
use IgniTest\Fixtures\Playlist\PlaylistRepository;
use IgniTest\Fixtures\Track\TrackRepository;

trait StorageTrait
{
    /** @var Storage */
    private $unitOfWork;

    /** @var string */
    private $sqliteDbPath;

    /** @var string */
    private $mongoData;

    /** @var SqliteConnection */
    private $sqliteConnection;

    /** @var MongoDBConnection */
    private $mongoConnection;

    /** @var EntityManager */
    private $entityManager;

    private function setupStorage(): void
    {
        ConnectionManager::release();
        $tmpDir = __DIR__ . '/../../tmp/';
        // Copy database so it stays untouched.
        $dbDir = __DIR__ . '/../../Fixtures/';
        $this->sqliteDbPath = tempnam(sys_get_temp_dir(), 'igni-test');
        copy($dbDir . '/test.db', $this->sqliteDbPath);



        $this->sqliteConnection = new SqliteConnection('sqlite:' . $this->sqliteDbPath);
        $this->mongoConnection = new MongoDBConnection('localhost', new MongoDBOptions('test', 'travis', 'test'));
        ConnectionManager::register($this->sqliteConnection);
        ConnectionManager::register($this->mongoConnection, 'mongo');

        $this->entityManager = new EntityManager($tmpDir);
        $this->unitOfWork = new Storage($this->entityManager);

        $mongoData = json_decode(file_get_contents($dbDir . '/test.json'), true);
        foreach ($mongoData as $collection => $data) {
            try {
                $this->mongoConnection->dropCollection($collection);
            } catch (\Exception $e) {
                // Ignore missing collection error.
            }
            $this->mongoConnection->insert($collection, ...$data);
        }
    }

    private function executeSql(string $query, array $bind = null): \PDOStatement
    {
        $statement = $this->sqliteConnection->getBaseConnection()->prepare($query);
        $statement->execute($bind);

        return $statement;
    }

    private function clearStorage(): void
    {
        unlink($this->sqliteDbPath);
    }

    private function createCursorForSql(string $sql, array $bind = null): Cursor
    {
        return $this->sqliteConnection->execute($sql, $bind);
    }

    private function loadRepositories(): void
    {
        $this->entityManager->addRepository(
            $this->createArtistRepository(),
            $this->createAlbumRepository(),
            $this->createTrackRepository(),
            $this->createGenreRepository(),
            $this->createPlaylistRepository()
        );
    }

    private function createArtistRepository(): ArtistRepository
    {
        return new ArtistRepository($this->entityManager);
    }

    private function createAlbumRepository(): AlbumRepository
    {
        return new AlbumRepository($this->entityManager);
    }

    private function createTrackRepository(): TrackRepository
    {
        return new TrackRepository($this->entityManager);
    }

    private function createGenreRepository(): GenreRepository
    {
        return new GenreRepository($this->entityManager);
    }

    private function createPlaylistRepository(): PlaylistRepository
    {
        return new PlaylistRepository($this->entityManager);
    }
}
