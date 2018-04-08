<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Artist;

use Igni\Storage\Driver\Pdo\Repository;
use Igni\Storage\Mapping\Schema;

class ArtistRepository extends Repository
{
    public function findByTrackId($id): ArtistEntity
    {
        $query = "SELECT 
          artists.* FROM artists 
            JOIN albums ON albums.ArtistId = artists.ArtistId 
            JOIN tracks ON tracks.AlbumId = albums.AlbumId
          WHERE tracks.TrackId = :id
        ";

        $cursor = $this->connection->execute($query, ['id' => $id]);
        $cursor->hydrateTo(ArtistEntity::class);

        return $cursor->current();
    }

    public function getEntity(): Schema
    {
        return ArtistEntity::class;
    }
}
