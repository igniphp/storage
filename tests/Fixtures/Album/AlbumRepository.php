<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Album;

use Igni\Storage\Driver\Pdo\Repository;
use Igni\Storage\Mapping\ImmutableCollection;
use IgniTest\Fixtures\Artist\ArtistEntity;

class AlbumRepository extends Repository
{
    public function getAll()
    {
        return $this->query('SELECT * FROM albums');
    }

    public function findByArtist(ArtistEntity $artist): ImmutableCollection
    {
        $query = "SELECT * FROM albums WHERE ArtistId = :id";

        return new ImmutableCollection($this->query($query, ['id' => $artist->getId()->getValue()]));
    }

    public function getEntityClass(): string
    {
        return AlbumEntity::class;
    }
}
