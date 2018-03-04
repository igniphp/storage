<?php declare(strict_types=1);

namespace Igni\Storage\Exception;

use Igni\Storage\Entity;

class IdentityMapException extends StorageException
{
    public static function forEntityWithoutIdentity(Entity $entity): IdentityMapException
    {
        $class = get_class($entity);
        return new self("${class} has no identity, thus it cannot be attached to IdentityMap.");
    }

    public static function forNonExistingEntity($id): IdentityMapException
    {
        return new self("Object with {$id} was not found in IdentityMap.");
    }
}
