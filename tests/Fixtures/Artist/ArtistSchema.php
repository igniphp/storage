<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Artist;

use Igni\Storage\Driver\EntityManager;
use Igni\Storage\Mapping\NamingStrategy;
use Igni\Storage\Mapping\Type;
use Igni\Storage\Mapping\Schema;
use IgniTest\Fixtures\Album\AlbumEntity;

class ArtistSchema extends Schema
{
    protected function define(): void
    {
        $this->map('id', Type::id());
        $this->map('name', Type::string());
        $this->map('albums', Type::define(
            function($row, EntityManager $manager) {
                return $manager
                    ->getRepository(AlbumEntity::class)
                    ->findByArtistId($row['ArtistId']);
            }
        ));

        $this->setNamingStrategy(new NamingStrategy\DirectNaming([
            'id' => 'ArtistId',
            'name' => 'Name',
        ]));
    }

    public function getSource(): string
    {
        return 'artists';
    }

    public function getEntity(): string
    {
        return ArtistEntity::class;
    }
}
