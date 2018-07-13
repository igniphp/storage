<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Album;

use Igni\Storage\Driver\Pdo\Repository;
use Igni\Storage\Mapping\Collection\LazyCollection;
use IgniTest\Fixtures\Artist\ArtistEntity;

class AlbumRepository extends Repository
{
    public function getAll()
    {
        return $this->query('SELECT * FROM albums');
    }

    public function findByArtist(ArtistEntity $artist): LazyCollection
    {
        $query = "SELECT * FROM albums WHERE ArtistId = :id";

        return new LazyCollection($this->query($query, ['id' => $artist->getId()->getValue()]));
    }

    public static function getEntityClass(): string
    {
        return AlbumEntity::class;
    }
}
