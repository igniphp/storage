<?php declare(strict_types=1);

namespace Igni\Storage\Mapping;

use Igni\Storage\Driver\EntityManager;
use Igni\Storage\Hydration\HydratorFactory;
use Igni\Storage\Hydration\ObjectHydrator;

final class MappingContext
{
    private $entityClass;
    private $entityManager;
    private $hydratorFactory;

    public function __construct(string $entityClass, HydratorFactory $hydratorFactory, EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->entityClass = $entityClass;
        $this->hydratorFactory = $hydratorFactory;
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    public function getHydratorFor(string $entityClass): ObjectHydrator
    {

    }
}
