<?php declare(strict_types=1);

namespace Igni\Storage;

interface Repository
{
    /**
     * @param string|int $id
     * @return object|Storable
     */
    public function get($id): Storable;

    /**
     * @param Storable $entity
     * @return Storable
     */
    public function create(Storable $entity): Storable;

    /**
     * @param Storable $entity
     * @return Storable
     */
    public function remove(Storable $entity): Storable;

    /**
     * @param Storable $entity
     * @return Storable
     */
    public function update(Storable $entity): Storable;

    /**
     * @return string
     */
    public static function getEntityClass(): string;
}
