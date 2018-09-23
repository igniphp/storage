<?php declare(strict_types=1);

namespace Igni\Tests\Fixtures\Genre;

use Igni\Storage\Driver\Pdo\Repository;

class GenreRepository extends Repository
{
    public static function getEntityClass(): string
    {
        return GenreEntity::class;
    }
}
