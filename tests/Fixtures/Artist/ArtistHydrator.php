<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Artist;

use Igni\Storage\EntityManager;
use IgniTest\Fixtures\Album\AlbumEntity;

class ArtistHydrator
{
    private $entityManager;

    public function __construct(EntityManager $manager)
    {
        $this->entityManager = $manager;
    }

    public function hydrateAlbums(ArtistEntity $artistEntity)
    {
        $artistEntity->setAlbums(
            $this->entityManager
                ->getRepository(AlbumEntity::class)
                ->findByArtist($artistEntity)
        );
    }
}
