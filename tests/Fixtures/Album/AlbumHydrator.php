<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Album;

use Igni\Storage\EntityManager;
use Igni\Storage\Mapping\ImmutableCollection;
use IgniTest\Fixtures\Track\TrackEntity;
use IgniTest\Fixtures\Track\TrackRepository;

class AlbumHydrator
{
    private $entityManager;
    private $metaData;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->metaData = $entityManager->getMetaData(AlbumEntity::class);
    }

    public function hydrateTracks(AlbumEntity $entity): void
    {
        /** @var TrackRepository $repository */
        $repository = $this->entityManager->getRepository(TrackEntity::class);
        $this->metaData->getProperty('tracks')->setValue($entity, $repository->findByAlbum($entity));
    }
}
