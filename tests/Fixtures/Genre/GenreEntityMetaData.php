<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Genre;

use Igni\Storage\Driver\EntityManager;
use Igni\Storage\Mapping\Type;
use Igni\Storage\Mapping\NamingStrategy\DirectNaming;
use Igni\Storage\Mapping\EntityMetaData;
use IgniTest\Fixtures\Track\TrackEntity;

class GenreEntityMetaData extends EntityMetaData
{
    protected function define(): void
    {
        $this->setNamingStrategy(new DirectNaming([
            'id' => 'GenreId',
            'name' => 'Name',
        ]));

        $this->map('id', Type::id());
        $this->map('name', Type::string());
        $this->map('tracks', Type::define(
            function($row, EntityManager $manager) {
                return $manager->getRepository(TrackEntity::class)
                    ->findByGenreId($row['GenreId']);
            }
        ));
    }

    public function getSource(): string
    {
        return 'genres';
    }

    public function getEntity(): string
    {
        return GenreEntity::class;
    }
}
