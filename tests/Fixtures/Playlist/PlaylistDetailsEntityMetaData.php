<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Playlist;

use Igni\Storage\Driver\EntityManager;
use Igni\Storage\Mapping\Type;
use Igni\Storage\Mapping\EntityMetaData;
use IgniTest\Fixtures\Track\TrackEntity;

class PlaylistDetailsEntityMetaData extends EntityMetaData
{
    protected function define(): void
    {
        $this->map('rating', Type::float());
        $this->map('tracks', Type::define(
            function ($row, EntityManager $manager) {
                return $manager->getRepository(TrackEntity::class)
                    ->getMultiple($row['songs']);
            },
            function (PlaylistDetails $entity) {
                $tracks = [];
                /** @var TrackEntity $track */
                foreach ($entity->getTracks() as $track) {
                    $tracks[] = $track->getId()->getValue();
                }

                return ['songs' => $tracks];
            }
        ));
    }

    public function getEntity(): string
    {
        return PlaylistDetails::class;
    }
}
