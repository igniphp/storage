<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Playlist;

use Igni\Storage\EntityManager;
use IgniTest\Fixtures\Track\TrackEntity;

class PlaylistDetailsHydrator
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function hydrateTracks(PlaylistDetails $entity, array &$data)
    {
        $tracks = $this->entityManager->getRepository(TrackEntity::class)
            ->getMultiple($data['songs']);

        $entity->setTracks($tracks);
    }

    public function extractTracks(PlaylistDetails $entity, array &$data): void
    {
        $tracks = [];
        /** @var TrackEntity $track */
        foreach ($entity->getTracks() as $track) {
            $tracks[] = $track->getId()->getValue();
        }

        $data['songs'] = $tracks;
    }
}
