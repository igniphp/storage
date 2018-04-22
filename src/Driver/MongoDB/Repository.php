<?php declare(strict_types=1);

namespace Igni\Storage\Driver\MongoDB;

use Igni\Storage\Repository as RepositoryInterface;
use Igni\Storage\EntityManager;
use Igni\Storage\Entity;

abstract class Repository implements RepositoryInterface
{
    protected $connection;
    protected $aggregate;
    protected $entityManager;
    protected $hydrator;

    final public function __construct(Connection $connection, EntityManager $entityManager)
    {
        $this->connection = $connection;
        $this->entityManager = $entityManager;
        $this->hydrator = $entityManager->getHydrator($this->getEntityClass());
    }

    public function get($id): Entity
    {
        $cursor = $this->connection->find($this->getSchema()->getSource(), ['_id' => $id], ['limit' => 1]);
        $cursor->setHydrator($this->hydrator);

        $entity = $cursor->current();
        $cursor->close();
        return $entity;
    }

    public function create(Entity $entity): Entity
    {
        $data = $this->hydrator->extract($entity);
        $this->connection->insert($this->getSchema()->getSource(), $data);

        return $entity;
    }

    public function remove(Entity $entity): Entity
    {
        $this->connection->remove($this->getSchema()->getSource(), $entity->getId()->getValue());

        return $entity;
    }

    public function update(Entity $entity): Entity
    {
        $this->connection->update($this->getSchema()->getSource(), $this->hydrator->extract($entity));
    }

    abstract public function getEntityClass(): string;
}
