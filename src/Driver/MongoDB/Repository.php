<?php declare(strict_types=1);

namespace Igni\Storage\Driver\MongoDB;

use Igni\Storage\Driver\GenericRepository;
use Igni\Storage\Exception\RepositoryException;
use Igni\Storage\Storable;

abstract class Repository extends GenericRepository
{
    public function get($id): Storable
    {
        $cursor = $this->connection->find(
            $this->metaData->getSource(),
            ['_id' => $id],
            ['limit' => 1]
        );
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
        // Support id auto-generation.
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

    public function remove(Storable $entity): Storable
    {
        $this->connection->remove(
            $this->metaData->getSource(),
            $entity->getId()->getValue()
        );

        return $entity;
    }

    public function update(Storable $entity): Storable
    {
        $this->connection->update(
            $this->metaData->getSource(),
            $this->hydrator->extract($entity)
        );
    }
}
