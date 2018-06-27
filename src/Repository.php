<?php declare(strict_types=1);

namespace Igni\Storage;

interface Repository
{
    /**
     * @param mixed $id
     * @return Entity
     */
    public function get($id): Entity;

    /**
     * @param Entity $entity
     * @return Entity
     */
    public function create(Entity $entity): Entity;

    /**
     * @param Entity $entity
     * @return Entity
     */
    public function remove(Entity $entity): Entity;

    /**
     * @param Entity $entity
     * @return Entity
     */
    public function update(Entity $entity): Entity;

    /**
     * @return string
     */
    public function getEntityClass(): string;
}
