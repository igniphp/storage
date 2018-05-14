<?php declare(strict_types=1);

namespace Igni\Storage\Hydration;

use Igni\Storage\EntityManager;
use Igni\Storage\Mapping\MetaData\EntityMetaData;

abstract class GenericHydrator implements ObjectHydrator
{
    protected $entityManager;
    protected $metaData;
    protected $mode;

    public function __construct(EntityManager $entityManager, string $mode = HydrationMode::BY_REFERENCE)
    {
        $this->mode = $mode;
        $this->entityManager = $entityManager;
        $this->metaData = $entityManager->getMetaData($this->getEntityClass());
    }

    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }

    public function getMode(): string
    {
        return $this->mode;
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
