<?php declare(strict_types=1);

namespace Igni\Storage\Mapping;

use Igni\Storage\EntityManager;

final class MappingContext
{
    private $entityClass;
    private $entityManager;

    public function __construct(string $entityClass, EntityManager $entityManager)
    {
        $this->entityClass = $entityClass;
        $this->entityManager = $entityManager;
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }
}
