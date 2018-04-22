<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Album;

use Igni\Storage\EntityManager;
use Igni\Storage\Mapping\ImmutableCollection;
use IgniTest\Fixtures\Track\TrackEntity;

class AlbumHydrator
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function hydrateTracks(array $data, AlbumEntity $entity): ImmutableCollection
    {
        return $this->entityManager->getRepository(TrackEntity::class)
            ->getMultiple();
    }
}
