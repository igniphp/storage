<?php declare(strict_types=1);

namespace Igni\Storage\Exception;

class RepositoryException extends StorageException
{
    public static function forNotFound($id): self
    {
        return new self("Entity with id `${id}` was not found");
    }

    public static function forInvalidRepository($repository): self
    {
        return new self(
            "Repository ($repository) should be either instance of Repository interface or 
            string containing class name which extends GenericRepository class."
        );
    }
}
