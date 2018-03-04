<?php declare(strict_types=1);

namespace IgniTest\Fixtures\Genre;

use Igni\Storage\Driver\Pdo\Repository;
use Igni\Storage\Mapping\Schema;

class GenreRepository extends Repository
{
    public function getSchema(): Schema
    {
        return GenreSchema::instance();
    }
}
