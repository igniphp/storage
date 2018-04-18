<?php declare(strict_types=1);

namespace Igni\Storage\Driver\Pdo;

use Igni\Storage\EntityManager;
use Igni\Storage\Entity;
use Igni\Storage\Exception\RepositoryException;
use Igni\Storage\Hydration\Hydrator;
use Igni\Storage\Mapping\EntityMetaData;
use Igni\Storage\Repository as RepositoryInterface;

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
    }

    public function get($id): Entity
    {
        if ($this->entityManager->has($this->getEntityClass(), $id)) {
            return $this->entityManager->get($this->getEntityClass(), $id);
        }
        $schema = $this->getSchema();
        $query = sprintf('SELECT *FROM %s WHERE %s = :__id__', $schema->getSource(), $schema->getId());

        $cursor = $this->connection->execute($query, ['__id__' => $id]);
        $cursor->setHydrator($this->hydrator);
        $entity = $cursor->current();
        if (!$entity) {
            throw RepositoryException::forNotFound($id);
        }

        return $entity;
    }

    public function create(Entity $entity): Entity
    {
        $schema = $this->getSchema();
        $data = $this->hydrator->extract($entity);
        $fields = array_keys($data);
        $binds = [];
        $columns = [];
        foreach ($fields as $columnName) {
            $columns[] = "\"${columnName}\"";
            $binds[] = ":${columnName}";
        }
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES(%s)',
            $schema->getSource(),
            implode(',', $columns),
            implode(',', $binds)
        );
        $cursor = $this->connection->execute($sql, $data);
        $cursor->execute();
        return $entity;
    }

    public function remove(Entity $entity): Entity
    {
        $schema = $this->getSchema();
        $query = sprintf('DELETE FROM %s WHERE %s = :__id__', $schema->getSource(), $schema->getId());
        $cursor = $this->connection->execute($query, ['__id__' => $entity->getId()]);
        $cursor->execute();

        return $entity;
    }

    public function update(Entity $entity): Entity
    {
        $schema = $this->getSchema();
        $data = $this->hydrator->extract($entity);
        $fields = array_keys($data);
        foreach ($fields as $columnName) {
            $columns[] = "\"${columnName}\" = :${columnName}";
        }
        $sql = sprintf('UPDATE %s SET %s WHERE %s = :__id__', $schema->getSource(), implode(', ', $columns), $schema->getId());
        $cursor = $this->connection->execute($sql, array_merge($data, ['__id__' => $entity->getId()]));
        $cursor->execute();
        $cursor->close();

        return $entity;
    }

    public function getEntityClass(): string
    {
        if ($this->entityClass) {
            return $this->entityClass;
        }

        return $this->entityClass = $this->getSchema()->getEntity();
    }
}
