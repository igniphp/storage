<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Artist;

use Igni\Storage\EntityManager;
use IgniTest\Fixtures\Album\AlbumEntity;

class ArtistHydrator
{
    public function hydrateAlbums(array $data, EntityManager $manager)
    {
        return $manager
            ->getRepository(AlbumEntity::class)
            ->findByArtistId($data['ArtistId']);
    }
}
