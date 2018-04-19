<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Playlist;

use Igni\Storage\Driver\MongoDB\Repository;
use Igni\Storage\Mapping\MetaData\EntityMetaData;

class PlaylistRepository extends Repository
{
    public function getEntityClass(): string
    {
        return PlaylistEntity::class;
    }
}
