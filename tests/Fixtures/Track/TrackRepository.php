<?php declare(strict_types=1);

namespace Igni\Tests\Fixtures\Track;

use Igni\Storage\Driver\Pdo\Repository;
use Igni\Storage\Mapping\Collection\LazyCollection;
use Igni\Tests\Fixtures\Album\AlbumEntity;

class TrackRepository extends Repository
{
    public function getMultiple(array $ids): LazyCollection
    {
        $bind = implode(',', array_fill(0, count($ids), '?'));
        $query = "SELECT * FROM tracks WHERE TrackId IN(${bind})";

        $cursor = $this->connection->createCursor($query, $ids);
        $cursor->hydrateWith($this->hydrator);

        return new LazyCollection($cursor);
    }

    public function findByGenreId($id): LazyCollection
    {
        $query = "SELECT * FROM tracks WHERE GenreId = :id";

        $cursor = $this->connection->createCursor($query, ['id' => $id]);
        $cursor->hydrateWith($this->hydrator);

        return new LazyCollection($cursor);
    }

    public function findByAlbum(AlbumEntity $album): LazyCollection
    {
        $query = "SELECT * FROM albums WHERE AlbumId = :id";

        $cursor = $this->connection->createCursor($query, ['id' => $album->getId()->getValue()]);
        $cursor->hydrateWith($this->hydrator);

        return new LazyCollection($cursor);
    }

    public static function getEntityClass(): string
    {
        return TrackEntity::class;
    }
}
