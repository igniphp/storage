<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Album;

use Igni\Storage\Driver\Pdo\Repository;
use Igni\Storage\Mapping\ImmutableCollection;
use Igni\Storage\Mapping\EntityMetaData;

class AlbumRepository extends Repository
{
    public function getAll()
    {
        $query = 'SELECT * FROM albums';

        $cursor = $this->connection->execute($query);
        $cursor->setHydrator($this->hydrator);

        return $cursor;
    }

    public function findByArtistId($artistId): ImmutableCollection
    {
        $query = "SELECT * FROM albums WHERE ArtistId = :id";

        $cursor = $this->connection->execute($query, ['id' => $artistId]);
        $cursor->setHydrator($this->hydrator);

        return new ImmutableCollection($cursor);
    }

    public function getSchema(): EntityMetaData
    {
        return AlbumMetaData::instance();
    }
}
