<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Playlist;

use Igni\Storage\Driver\MongoDB\Repository;

class PlaylistRepository extends Repository
{
    public function getEntityClass(): string
    {
        return PlaylistEntity::class;
    }
}
