<?php declare(strict_types=1);

namespace Igni\Storage\Hydration;

use Igni\Storage\EntityManager;
use Igni\Storage\Mapping\MetaData\EntityMetaData;

abstract class GenericHydrator implements MemorySavingHydrator
{
    protected $entityManager;
    protected $metaData;
    protected $saveMemory = false;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->metaData = $entityManager->getMetaData($this->getEntityClass());
    }

    public function saveMemory(bool $save = true): void
    {
        $this->saveMemory = $save;
    }

    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    public function getMetaData(): EntityMetaData
    {
        return $this->metaData;
    }

    abstract public static function getEntityClass(): string;
}
