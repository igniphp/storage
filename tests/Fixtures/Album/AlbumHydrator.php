<?php declare(strict_types=1);

namespace Igni\Tests\Fixtures\Album;

use Igni\Storage\Hydration\GenericHydrator;
use Igni\Storage\Hydration\ObjectHydrator;
use Igni\Tests\Fixtures\Track\TrackEntity;
use Igni\Tests\Fixtures\Track\TrackRepository;

class AlbumHydrator implements ObjectHydrator
{
    private $baseHydrator;

    public function __construct(GenericHydrator $baseHydrator)
    {
        $this->baseHydrator = $baseHydrator;
    }

    public function hydrate(array $data): AlbumEntity
    {
        /** @var AlbumEntity $entity */
        $entity = $this->baseHydrator->hydrate($data);
        $this->hydrateTracks($entity);

        return $entity;
    }

    public function extract($entity): array
    {
        return $this->baseHydrator->extract($entity);
    }

    private function hydrateTracks(AlbumEntity $entity): void
    {
        /** @var TrackRepository $repository */
        $repository = $this->baseHydrator->getEntityManager()->getRepository(TrackEntity::class);
        $entity->setTracks($repository->findByAlbum($entity));
    }
}
