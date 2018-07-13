<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Artist;

use Igni\Storage\Driver\Pdo\Repository;

class ArtistRepository extends Repository
{
    public function findByTrackId($id): ArtistEntity
    {
        $cursor = $this->query("SELECT 
          artists.* FROM artists 
            JOIN albums ON albums.ArtistId = artists.ArtistId 
            JOIN tracks ON tracks.AlbumId = albums.AlbumId
          WHERE tracks.TrackId = :id
        ", [
            'id' => $id
        ]);

        return $cursor->current();
    }

    public static function getEntityClass(): string
    {
        return ArtistEntity::class;
    }
}
