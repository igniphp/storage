<?php declare(strict_types=1);

namespace Igni\Storage\Driver\Pdo;

use Igni\Storage\Storable;
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

    public function get($id): Storable
    {
        if ($this->entityManager->has($this->getEntityClass(), $id)) {
            return $this->entityManager->get($this->getEntityClass(), $id);
        }

        $cursor = $this->buildSelectQuery($id);
        $cursor->hydrateWith($this->hydrator);
        $entity = $cursor->current();
        $cursor->close();

        if (!$entity instanceof Storable) {
            throw RepositoryException::forNotFound($id);
        }

        return $entity;
    }

    public function create(Storable $entity): Storable
    {
        // Execute id auto-generation.
        $entity->getId();
        $cursor = $this->buildCreateQuery($entity);
        $cursor->execute();

        return $entity;
    }

    public function remove(Storable $entity): Storable
    {
        $cursor = $this->buildDeleteQuery($entity);
        $cursor->execute();

        return $entity;
    }

    public function update(Storable $entity): Storable
    {
        $cursor = $this->buildUpdateQuery($entity);
        $cursor->execute();

        return $entity;
    }

    protected function query($query, array $parameters = []): Cursor
    {
        $cursor = $this->connection->execute($query, $parameters);
        $cursor->hydrateWith($this->hydrator);
        return $cursor;
    }

    protected function buildSelectQuery($id): Cursor
    {
        $query = sprintf(
            'SELECT *FROM %s WHERE %s = :_id',
            $this->metaData->getSource(),
            $this->metaData->getIdentifier()->getFieldName()
        );

        return $this->connection->execute($query, ['_id' => $id]);
    }

    protected function buildDeleteQuery(Storable $entity): Cursor
    {
        $query = sprintf(
            'DELETE FROM %s WHERE %s = :_id',
            $this->metaData->getSource(),
            $this->metaData->getIdentifier()->getFieldName()
        );
        return $this->connection->execute($query, ['_id' => $entity->getId()]);
    }

    protected function buildCreateQuery(Storable $entity): Cursor
    {
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
        return $this->connection->execute($sql, $data);
    }

    protected function buildUpdateQuery(Storable $entity): Cursor
    {
        $data = $this->hydrator->extract($entity);
        $fields = array_keys($data);
        $columns = [];
        foreach ($fields as $columnName) {
            $columns[] = "\"${columnName}\" = :${columnName}";
        }
        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s = :_id',
            $this->metaData->getSource(),
            implode(', ', $columns),
            $this->metaData->getIdentifier()->getFieldName()
        );
        return $this->connection->execute($sql, array_merge($data, ['_id' => $entity->getId()]));
    }

    abstract public function getEntityClass(): string;
}
