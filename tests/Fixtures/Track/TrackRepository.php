<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Track;

use Igni\Storage\Driver\Pdo\Repository;
use Igni\Storage\Mapping\Collection\LazyCollection;
use Igni\Storage\Mapping\MetaData\EntityMetaData;
use IgniTest\Fixtures\Album\AlbumEntity;

class TrackRepository extends Repository
{
    public function getMultiple(array $ids): LazyCollection
    {
        $bind = implode(',', array_fill(0, count($ids), '?'));
        $query = "SELECT * FROM tracks WHERE TrackId IN(${bind})";

        $cursor = $this->connection->execute($query, $ids);
        $cursor->setHydrator($this->hydrator);

        return new LazyCollection($cursor);
    }

    public function findByGenreId($id): LazyCollection
    {
        $query = "SELECT * FROM tracks WHERE GenreId = :id";

        $cursor = $this->connection->execute($query, ['id' => $id]);
        $cursor->setHydrator($this->hydrator);

        return new LazyCollection($cursor);
    }

    public function findByAlbum(AlbumEntity $album): LazyCollection
    {
        $query = "SELECT * FROM albums WHERE AlbumId = :id";

        $cursor = $this->connection->execute($query, ['id' => $album->getId()->getValue()]);
        $cursor->setHydrator($this->hydrator);

        return new LazyCollection($cursor);
    }

    public function getEntityClass(): string
    {
        return TrackEntity::class;
    }
}
