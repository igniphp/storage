<?php declare(strict_types=1);

namespace Igni\Storage\Hydration;

use Igni\Storage\Storable;

interface ObjectHydrator
{
    /**
     * Returns entity hydrated with provided data
     *
     * @param array $data
     * @return object|Storable
     */
    public function hydrate(array $data);

    /**
     * Returns values extracted from an object
     *
     * @param object|Entity $entity
     * @return array
     */
    public function extract($entity): array;
}
