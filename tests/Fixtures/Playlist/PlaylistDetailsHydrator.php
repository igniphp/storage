<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Playlist;

use Igni\Storage\Driver\EntityManager;
use IgniTest\Fixtures\Track\TrackEntity;

class PlaylistDetailsHydrator
{
    public function hydrateTracks($row, EntityManager $manager)
    {
        return $manager->getRepository(TrackEntity::class)
            ->getMultiple($row['songs']);
    }

    public function extractTracks(PlaylistDetails $entity)
    {
        $tracks = [];
        /** @var TrackEntity $track */
        foreach ($entity->getTracks() as $track) {
            $tracks[] = $track->getId()->getValue();
        }

        return ['songs' => $tracks];
    }
}
