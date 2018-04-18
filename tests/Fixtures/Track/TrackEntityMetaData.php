<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Track;

use Igni\Storage\Driver\EntityManager;
use Igni\Storage\Mapping\NamingStrategy\DirectNaming;
use Igni\Storage\Mapping\EntityMetaData;
use Igni\Storage\Mapping\Type;
use IgniTest\Fixtures\Album\AlbumEntity;
use IgniTest\Fixtures\Genre\GenreEntity;

class TrackEntityMetaData extends EntityMetaData
{
    protected function define(): void
    {
        $this->setNamingStrategy(new DirectNaming([
            'id' => 'TrackId',
            'name' => 'Name',
            'length' => 'Milliseconds',
            'size' => 'Bytes',
            'unitPrice' => 'UnitPrice',
            'mediaType' => 'MediaTypeId',
            'genre' => 'GenreId',
            'composer' => 'Composer',
            'album' => 'AlbumId',
        ]));

        $this->map('id', Type::id());
        $this->map('name', Type::string());
        $this->map('length', Type::integer());
        $this->map('size', Type::integer());
        $this->map('unitPrice', Type::float());
        $this->map('composer', Type::string());
        $this->map('mediaType', Type::string());
        $this->map('name', Type::string());
        $this->map('genre', Type::reference(GenreEntity::class));
        $this->map('album', Type::reference(AlbumEntity::class));
        $this->map('artist', Type::define(function($row, EntityManager $manager) {
            return $manager->get(AlbumEntity::class, $row['AlbumId'])->getArtist();
        }));
    }

    public function getSource(): string
    {
        return 'tracks';
    }

    public function getEntity(): string
    {
        return TrackEntity::class;
    }
}
