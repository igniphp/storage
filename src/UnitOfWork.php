<?php declare(strict_types=1);

namespace Igni\Storage;

interface UnitOfWork
{
    /**
     * Returns already stored entity, entity is retrieved by id.
     * @param string $entity
     * @param $id
     * @return Entity
     */
    public function get(string $entity, $id): Entity;

    /**
     * Adds entity(ies) for further save
     *
     * @param Entity[] ...$entities
     */
    public function persist(Entity ...$entities): void;

    /**
     * Adds entity(ies) for further deletion
     * @param Entity[] ...$entities
     */
    public function remove(Entity ...$entities): void;

    /**
     * Removes and saves previously added entities to the Unit
     */
    public function commit(): void;

    /**
     * Previously added entities will not be persisted nor removed
     */
    public function rollback(): void;

    /**
     * @param Entity[] ...$entities
     */
    public function detach(Entity ...$entities): void;

    /**
     * @param Entity[] ...$entities
     */
    public function attach(Entity ...$entities): void;

    /**
     * @param Entity $entity
     * @return bool
     */
    public function contains(Entity $entity): bool;
}
