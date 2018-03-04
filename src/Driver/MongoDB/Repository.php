<?php declare(strict_types=1);

namespace Igni\Storage\Driver\MongoDB;

use Igni\Storage\Mapping\NamingStrategy\DefaultNamingStrategy;
use Igni\Storage\Mapping\NamingStrategy\DirectNaming;
use Igni\Storage\Repository as RepositoryInterface;
use Igni\Storage\Driver\EntityManager;
use Igni\Storage\Entity;
use Igni\Storage\Hydration\Hydrator;
use Igni\Storage\Mapping\Schema;

abstract class Repository implements RepositoryInterface
{
    protected $connection;
    protected $aggregate;
    protected $entityManager;
    protected $hydrator;

    private $entityClass;

    final public function __construct(Connection $connection, EntityManager $entityManager)
    {
        $this->connection = $connection;
        $this->entityManager = $entityManager;
        $this->hydrator = new Hydrator($entityManager, $this->getSchema());
        if ($this->hydrator->getNamingStrategy() instanceof DefaultNamingStrategy) {
            $this->hydrator->setNamingStrategy(new DirectNaming([]));
        }
        $this->hydrator->getNamingStrategy()->addRule('id', '_id');
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
        $this->connection->remove($this->getSchema()->getSource(), $entity->getId());

        return $entity;
    }

    public function update(Entity $entity): Entity
    {
        $this->connection->update($this->getSchema()->getSource(), $this->hydrator->extract($entity));
    }

    public function getEntityClass(): string
    {
        if ($this->entityClass) {
            return $this->entityClass;
        }

        return $this->entityClass = $this->getSchema()->getEntity();
    }

    abstract public function getSchema(): Schema;
}
