<?php declare(strict_types=1);

namespace Igni\Storage\Driver;

use Igni\Application\Config;
use Igni\Storage\Exception\RepositoryException;
use Igni\Storage\Exception\StorageException;
use Igni\Storage\RepositoryContainer;
use Igni\Storage\Entity;
use Igni\Storage\Mapping\IdentityMap;
use Igni\Storage\Repository;

class EntityManager implements IdentityMap, RepositoryContainer
{
    /** @var Entity[] */
    private $registry;

    /** @var Repository[] */
    private $repositories = [];

    /** @var Connection[] */
    private $connections = [];

    public function create(Entity $entity): Entity
    {
        $this->getRepository(get_class($entity))->create($entity);
        $this->attach($entity);
        return $entity;
    }

    public function update(Entity $entity): Entity
    {
        $this->getRepository(get_class($entity))->update($entity);
        return $entity;
    }

    public function remove(Entity $entity): Entity
    {
        $this->getRepository(get_class($entity))->remove($entity);
        $this->detach($entity);

        return $entity;
    }

    public function get(string $entity, $id): Entity
    {
        $key = $this->getId($entity, $id);
        if ($this->has($entity, $id)) {
            return $this->registry[$key];
        }

        return $this->registry[$key] = $this->getRepository($entity)->get($id);
    }

    public function getRepository(string $entity): Repository
    {
        if ($this->hasRepository($entity)) {
            return $this->repositories[$entity];
        }

        throw RepositoryException::forNotRegisteredRepository($entity);
    }

    public function hasRepository(string $entity): bool
    {
        return isset($this->repositories[$entity]);
    }

    public function addRepository(string $entity, Repository $repository): void
    {
        $this->repositories[$entity] = $repository;
    }

    public function attach(Entity $entity): Entity
    {
        $key = $this->getId($entity);

        if (!isset($this->registry[$key])) {
            $this->registry[$key] = $entity;
        }

        return $this->registry[$key];
    }

    public function detach(Entity $entity): Entity
    {
        $key = $this->getId($entity);
        if (isset($this->registry[$key])) {
            unset($this->registry[$key]);
        }

        return $entity;
    }

    public function has(string $class, $id): bool
    {
        $key = $this->getId($class, $id);
        return isset($this->registry[$key]);
    }

    public function contains(Entity $entity): bool
    {
        return in_array($entity, $this->registry, true);
    }

    public function clear(): void
    {
        $this->registry = [];
    }

    private function getId($entity, $id = null): string
    {
        if ($entity instanceof Entity) {
            return get_class($entity) . '@' . $entity->getId();
        }

        return "${entity}@${id}";
    }

    public function addConnection(string $name, Connection $connection): void
    {
        $this->connections[$name] = $connection;
    }

    public function hasConnection(string $name): bool
    {
        return isset($this->connections[$name]);
    }

    public function getConnection(string $name): Connection
    {
        if (!$this->hasConnection($name)) {
            throw StorageException::forNotRegisteredConnection($name);
        }

        return $this->connections[$name];
    }

    public static function fromConfig(Config $config): EntityManager
    {

    }
}
