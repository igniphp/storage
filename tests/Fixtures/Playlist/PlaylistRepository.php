<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Playlist;

use Igni\Storage\Driver\MongoDB\Repository;
use Igni\Storage\Mapping\Schema;

class PlaylistRepository extends Repository
{
    public function getSchema(): Schema
    {
        return PlaylistSchema::instance();
    }
}
