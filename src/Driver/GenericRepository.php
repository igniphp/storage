<?php declare(strict_types=1);

namespace Igni\Storage\Driver;

use Igni\Storage\EntityManager;
use Igni\Storage\Hydration\ObjectHydrator;
use Igni\Storage\Mapping\MetaData\EntityMetaData;
use Igni\Storage\Repository as RepositoryInterface;

abstract class GenericRepository implements RepositoryInterface
{
    /**
     * @var Connection
     */
    protected $connection;
    /**
     * @var EntityManager
     */
    protected $entityManager;
    /**
     * @var ObjectHydrator
     */
    protected $hydrator;

    /**
     * @var EntityMetaData
     */
    protected $metaData;

    final public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->metaData = $this->entityManager->getMetaData($this->getEntityClass());
        $this->hydrator = $this->entityManager->getHydrator($this->getEntityClass());
        $this->connection = ConnectionManager::getConnection($this->metaData->getConnection());
    }
}
