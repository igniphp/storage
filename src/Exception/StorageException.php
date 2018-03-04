<?php declare(strict_types=1);

namespace Igni\Storage\Exception;

use Igni\Exception\RuntimeException;

class StorageException extends RuntimeException
{
    public static function forNotRegisteredRepository(string $class): self
    {
        return new self("Entity class ${class} has no repository assigned to it, have you forgot to call Ink::register()?");
    }

    public static function forNotRegisteredConnection(string $name): self
    {
        return new self("Connection with name ${name} was not registered.");
    }
}
