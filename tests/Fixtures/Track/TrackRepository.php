<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Track;

use Igni\Storage\Driver\Pdo\Repository;
use Igni\Storage\Mapping\ImmutableCollection;
use Igni\Storage\Mapping\MetaData\EntityMetaData;
use IgniTest\Fixtures\Album\AlbumEntity;

class TrackRepository extends Repository
{
    public function getMultiple(array $ids): ImmutableCollection
    {
        $bind = implode(',', array_fill(0, count($ids), '?'));
        $query = "SELECT * FROM tracks WHERE TrackId IN(${bind})";

        $cursor = $this->connection->execute($query, $ids);
        $cursor->setHydrator($this->hydrator);

        return new ImmutableCollection($cursor);
    }

    public function findByGenreId($id): ImmutableCollection
    {
        $query = "SELECT * FROM tracks WHERE GenreId = :id";

        $cursor = $this->connection->execute($query, ['id' => $id]);
        $cursor->setHydrator($this->hydrator);

        return new ImmutableCollection($cursor);
    }

    public function findByAlbum(AlbumEntity $album): ImmutableCollection
    {
        $query = "SELECT * FROM albums WHERE AlbumId = :id";

        $cursor = $this->connection->execute($query, ['id' => $album->getId()->getValue()]);
        $cursor->setHydrator($this->hydrator);

        return new ImmutableCollection($cursor);
    }

    public function getEntityClass(): string
    {
        return TrackEntity::class;
    }
}
