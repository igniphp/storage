<?php declare(strict_types=1);

namespace Igni\Storage\Mapping;

use Igni\Storage\Driver\EntityManager;
use Igni\Storage\Hydration\ObjectHydrator;

final class MappingContext
{
    private $entityClass;
    private $entityHydrator;
    private $entityManager;

    public function __construct(string $entityClass, ObjectHydrator $entityHydrator, EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->entityClass = $entityClass;
        $this->entityHydrator = $entityHydrator;
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function getEntityHydrator(): ObjectHydrator
    {
        return $this->entityHydrator;
    }

    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }
}
