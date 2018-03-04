<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Album;

use Igni\Storage\Driver\EntityManager;
use Igni\Storage\Mapping\Type;
use Igni\Storage\Mapping\NamingStrategy\DirectNaming;
use Igni\Storage\Mapping\Schema;
use IgniTest\Fixtures\Artist\ArtistEntity;
use IgniTest\Fixtures\Track\TrackEntity;

class AlbumSchema extends Schema
{
    public function define(): void
    {
        $this->setNamingStrategy(new DirectNaming([
            'id' => 'AlbumId',
            'title' => 'Title',
            'releaseDate' => 'ReleaseDate',
            'artist' => 'ArtistId',
        ]));

        $this->map('id', Type::id());
        $this->map('title', Type::string());
        $this->map('releaseDate', Type::date());
        $this->map('artist', Type::reference(ArtistEntity::class));
        $this->map('tracks', Type::define(function($data, EntityManager $manager) {
            return $manager->getRepository(TrackEntity::class)
                ->findByAlbumId($data['AlbumId']);
        }));
    }

    public function getEntity(): string
    {
        return AlbumEntity::class;
    }

    public function getSource(): string
    {
        return 'albums';
    }
}
