<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Genre;

use Igni\Storage\Driver\Pdo\Repository;

class GenreRepository extends Repository
{
    public function getEntityClass(): string
    {
        return GenreEntity::class;
    }
}
