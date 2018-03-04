<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Playlist;

use Igni\Storage\Driver\EntityManager;
use Igni\Storage\Mapping\Type;
use Igni\Storage\Mapping\Schema;
use IgniTest\Fixtures\Track\TrackEntity;

class PlaylistDetailsSchema extends Schema
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
                    $tracks[] = $track->getId();
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
