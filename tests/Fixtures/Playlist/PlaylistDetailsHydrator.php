<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Playlist;

use Igni\Storage\Hydration\GenericHydrator;
use Igni\Storage\Hydration\ObjectHydrator;
use IgniTest\Fixtures\Track\TrackEntity;

class PlaylistDetailsHydrator implements ObjectHydrator
{
    private $baseHydrator;

    public function __construct(GenericHydrator $baseHydrator)
    {
        $this->baseHydrator = $baseHydrator;
    }

    public function hydrate(array $data)
    {
        $entity = $this->baseHydrator->hydrate($data);
        $this->hydrateTracks($entity, $data['songs']);

        return $entity;
    }

    public function extract($entity): array
    {
        $data = $this->baseHydrator->extract($entity);
        $data['songs'] = $this->extractTracks($entity);

        return $data;
    }


    private function hydrateTracks(PlaylistDetails $entity, array $songs)
    {
        $tracks = $this->baseHydrator->getEntityManager()->getRepository(TrackEntity::class)
            ->getMultiple($songs);

        $entity->setTracks($tracks);
    }

    private function extractTracks(PlaylistDetails $entity): array
    {
        $tracks = [];
        /** @var TrackEntity $track */
        foreach ($entity->getTracks() as $track) {
            $tracks[] = $track->getId()->getValue();
        }

        return $tracks;
    }
}
