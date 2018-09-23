<?php declare(strict_types=1);

namespace Igni\Tests\Fixtures\Artist;

use Igni\Storage\Hydration\GenericHydrator;
use Igni\Storage\Hydration\ObjectHydrator;
use Igni\Tests\Fixtures\Album\AlbumEntity;

class ArtistHydrator implements ObjectHydrator
{
    private $baseHydrator;

    public function __construct(GenericHydrator $hydrator)
    {
        $this->baseHydrator = $hydrator;
    }

    public function hydrate(array $data): ArtistEntity
    {
        $entity = $this->baseHydrator->hydrate($data);
        $this->hydrateAlbums($entity);

        return $entity;
    }

    public function extract($entity): array
    {
        return $this->baseHydrator->extract($entity);
    }

    private function hydrateAlbums(ArtistEntity $artistEntity)
    {
        $artistEntity->setAlbums(
            $this->baseHydrator->getEntityManager()
                ->getRepository(AlbumEntity::class)
                ->findByArtist($artistEntity)
        );
    }
}
