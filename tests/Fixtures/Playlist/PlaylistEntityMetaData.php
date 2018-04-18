<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Playlist;

use Igni\Storage\Mapping\Strategy\Embed;
use Igni\Storage\Mapping\Type;
use Igni\Storage\Mapping\EntityMetaData;

class PlaylistEntityMetaData extends EntityMetaData
{
    protected function define(): void
    {
        $this->map('id', Type::id());
        $this->map('name', Type::string());
        $this->map('details', Type::embed(PlaylistDetailsEntityMetaData::instance(), Embed::STORAGE_PLAIN));
    }

    public function getSource(): string
    {
        return 'playlist';
    }

    public function getEntity(): string
    {
        return PlaylistEntity::class;
    }
}
