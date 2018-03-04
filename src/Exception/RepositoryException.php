<?php declare(strict_types=1);

namespace Igni\Storage\Exception;

class RepositoryException extends StorageException
{
    public static function forNotFound($id): RepositoryException
    {
        return new self("Entity with id `${id}` was not found");
    }
}
