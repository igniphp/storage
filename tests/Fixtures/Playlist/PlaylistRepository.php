<?php declare(strict_types=1);

namespace Igni\Tests\Fixtures\Playlist;

use Igni\Storage\Driver\MongoDB\Repository;

class PlaylistRepository extends Repository
{
    public static function getEntityClass(): string
    {
        return PlaylistEntity::class;
    }
}
