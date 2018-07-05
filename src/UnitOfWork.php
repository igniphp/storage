<?php declare(strict_types=1);

namespace Igni\Storage;

interface UnitOfWork
{
    /**
     * Returns already stored entity, entity is retrieved by id.
     * @param string $entity
     * @param $id
     * @return Storable
     */
    public function get(string $entity, $id): Storable;

    /**
     * Adds entity(ies) for further save
     *
     * @param Storable[] ...$entities
     */
    public function persist(Storable ...$entities): void;

    /**
     * Adds entity(ies) for further deletion
     * @param Storable[] ...$entities
     */
    public function remove(Storable ...$entities): void;

    /**
     * Removes and saves previously added entities to the Unit
     */
    public function commit(): void;

    /**
     * Previously added entities will not be persisted nor removed
     */
    public function rollback(): void;

    /**
     * @param Storable[] ...$entities
     */
    public function detach(Storable ...$entities): void;

    /**
     * @param Storable[] ...$entities
     */
    public function attach(Storable ...$entities): void;

    /**
     * @param Storable $entity
     * @return bool
     */
    public function contains(Storable $entity): bool;
}
