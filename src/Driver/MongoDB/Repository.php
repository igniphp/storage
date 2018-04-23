<?php declare(strict_types=1);

namespace Igni\Storage\Driver\MongoDB;

use Igni\Storage\Repository as RepositoryInterface;
use Igni\Storage\EntityManager;
use Igni\Storage\Entity;

abstract class Repository implements RepositoryInterface
{
    protected $connection;
    protected $entityManager;
    protected $hydrator;
    protected $metaData;

    final public function __construct(Connection $connection, EntityManager $entityManager)
    {
        $this->connection = $connection;
        $this->entityManager = $entityManager;
        $this->metaData = $entityManager->getMetaData($this->getEntityClass());
        $this->hydrator = $entityManager->getHydrator($this->getEntityClass());
    }

    public function get($id): Entity
    {
        $cursor = $this->connection->find(
            $this->metaData->getSource(),
            ['_id' => $id],
            ['limit' => 1]
        );
        $cursor->setHydrator($this->hydrator);

        $entity = $cursor->current();
        $cursor->close();

        return $entity;
    }

    public function create(Entity $entity): Entity
    {
        // Support id autogeneration
        $entity->getId();
        $data = $this->hydrator->extract($entity);
        if (isset($data['id'])) {
            $data['_id'] = $data['id'];
            unset($data['id']);
        }
        $this->connection->insert(
            $this->metaData->getSource(),
            $data
        );

        return $entity;
    }

    public function remove(Entity $entity): Entity
    {
        $this->connection->remove(
            $this->metaData->getSource(),
            $entity->getId()->getValue()
        );

        return $entity;
    }

    public function update(Entity $entity): Entity
    {
        $this->connection->update(
            $this->metaData->getSource(),
            $this->hydrator->extract($entity)
        );
    }

    abstract public function getEntityClass(): string;
}
