<?php declare(strict_types=1);

namespace Igni\Storage\Driver;

use Igni\Storage\EntityManager;
use Igni\Storage\Hydration\ObjectHydrator;
use Igni\Storage\Mapping\MetaData\EntityMetaData;
use Igni\Storage\Repository;

abstract class GenericRepository implements Repository
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

    public function __construct(EntityManager $entityManager, Connection $connection = null)
    {
        $this->entityManager = $entityManager;
        $this->metaData = $this->entityManager->getMetaData($this->getEntityClass());
        $this->hydrator = $this->entityManager->getHydrator($this->getEntityClass());
        if ($connection === null) {
            $connection = ConnectionManager::get($this->metaData->getConnection());
        }

        $this->connection = $connection;
    }
}
