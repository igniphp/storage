<?php declare(strict_types=1);

namespace Igni\Storage\Exception;

class UnitOfWorkException extends StorageException
{
    public static function forPersistingEntityInInvalidState($entity): self
    {
        $entity = get_class($entity);
        return new self("Cannot persist entity {$entity} which is in detached or removed state");
    }
}
