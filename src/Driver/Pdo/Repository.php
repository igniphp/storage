<?php declare(strict_types=1);

namespace Igni\Storage\Driver\Pdo;

use Igni\Storage\Entity;
use Igni\Storage\EntityManager;
use Igni\Storage\Exception\RepositoryException;
use Igni\Storage\Repository as RepositoryInterface;

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
        $this->metaData = $this->entityManager->getMetaData($this->getEntityClass());
        $this->hydrator = $this->entityManager->getHydrator($this->getEntityClass());
    }

    public function get($id): Entity
    {
        if ($this->entityManager->has($this->getEntityClass(), $id)) {
            return $this->entityManager->get($this->getEntityClass(), $id);
        }
        $query = sprintf(
            'SELECT *FROM %s WHERE %s = :__id__',
            $this->metaData->getSource(),
            $this->metaData->getIdentifier()->getFieldName()
        );

        $cursor = $this->connection->execute($query, ['__id__' => $id]);
        $cursor->setHydrator($this->hydrator);
        $entity = $cursor->current();

        if (!$entity instanceof Entity) {
            throw RepositoryException::forNotFound($id);
        }

        return $entity;
    }

    public function create(Entity $entity): Entity
    {
        // Execute id auto-generation.
        $entity->getId();
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
            $this->metaData->getSource(),
            implode(',', $columns),
            implode(',', $binds)
        );
        $cursor = $this->connection->execute($sql, $data);
        $cursor->execute();

        return $entity;
    }

    public function remove(Entity $entity): Entity
    {
        $query = sprintf(
            'DELETE FROM %s WHERE %s = :__id__',
            $this->metaData->getSource(),
            $this->metaData->getIdentifier()->getFieldName()
        );
        $cursor = $this->connection->execute($query, ['__id__' => $entity->getId()]);
        $cursor->execute();

        return $entity;
    }

    public function update(Entity $entity): Entity
    {
        $data = $this->hydrator->extract($entity);
        $fields = array_keys($data);
        $columns = [];
        foreach ($fields as $columnName) {
            $columns[] = "\"${columnName}\" = :${columnName}";
        }
        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s = :__id__',
            $this->metaData->getSource(),
            implode(', ', $columns),
            $this->metaData->getIdentifier()->getFieldName()
        );
        $cursor = $this->connection->execute($sql, array_merge($data, ['__id__' => $entity->getId()]));
        $cursor->execute();
        $cursor->close();

        return $entity;
    }

    protected function query($query, array $parameters = []): Cursor
    {
        $cursor = $this->connection->execute($query, $parameters);
        $cursor->setHydrator($this->hydrator);

        return $cursor;
    }

    abstract public function getEntityClass(): string;
}
