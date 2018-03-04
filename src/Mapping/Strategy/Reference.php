<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Strategy;

use Igni\Storage\Driver\EntityManager;
use Igni\Storage\Entity;
use Igni\Storage\Mapping\MappingStrategy;

final class Reference implements MappingStrategy
{
    private $entity;

    public function __construct(string $entity)
    {
        $this->entity = $entity;
    }

    public function hydrate($value, EntityManager $entityManager = null)
    {
        try {
            return $entityManager->get($this->entity, $value);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param Entity $value
     * @return mixed
     */
    public function extract($value)
    {
        if ($value instanceof Entity) {
            return $value->getId();
        }
    }
}
