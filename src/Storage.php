<?php declare(strict_types=1);

namespace Igni\Storage;

use Igni\Storage\Driver\ConnectionManager;
use Igni\Storage\Exception\UnitOfWorkException;
use SplObjectStorage;

class Storage implements UnitOfWork
{
    private const STATE_NEW = 1;
    private const STATE_MANAGED = 2;
    private const STATE_REMOVED = 3;
    private const STATE_DETACHED = 4;
    private const ACTION_CREATE = 'create';
    private const ACTION_REMOVE = 'remove';
    private const ACTION_UPDATE = 'update';

    /**
     * Contains calculated states for entities.
     * @var array<string, int>
     */
    private $states = [];

    /**
     * Contains grouped by class name entities that should be saved.
     * @var SplObjectStorage[]
     */
    private $create = [];

    /**
     * Contains grouped by class name entities that should be removed.
     * @var SplObjectStorage[]
     */
    private $remove = [];

    /**
     * Contains grouped by class name entities that should be removed.
     * @var SplObjectStorage[]
     */
    private $update = [];

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $manager = null)
    {
        $this->entityManager = $manager ?? new EntityManager();
    }

    public function getRepository(string $entity): Repository
    {
        return $this->entityManager->getRepository($entity);
    }

    public function hasRepository(string $entity): bool
    {
        return $this->entityManager->hasRepository($entity);
    }

    public function addRepository(Repository ...$repositories): void
    {
        $this->entityManager->addRepository(...$repositories);
    }

    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    public function get(string $entity, $id): Storable
    {
        $entity = $this->entityManager->get($entity, $id);
        $this->states[spl_object_hash($entity)] = self::STATE_MANAGED;

        return $entity;
    }

    /**
     * @param Storable[] ...$entities
     */
    public function persist(Storable ...$entities): void
    {
        foreach ($entities as $entity) {
            $this->persistOne($entity);
        }
    }

    /**
     * @param Storable[] ...$entities
     */
    public function remove(Storable ...$entities): void
    {
        foreach ($entities as $entity) {
            $this->removeOne($entity);
        }
    }

    public function commit(): void
    {
        $this->commitAction(self::ACTION_CREATE);
        $this->commitAction(self::ACTION_UPDATE);
        $this->commitAction(self::ACTION_REMOVE);
    }

    private function commitAction(string $action): void
    {
        foreach ($this->{$action} as $namespace => $entities) {
            foreach ($entities as $entity) {
                call_user_func([$this->entityManager->getRepository($namespace), $action], $entity);
            }
        }
    }

    public function rollback(): void
    {
        $this->create = [];
        $this->update = [];
        $this->remove = [];
    }

    private function persistOne(Storable $entity): void
    {
        $namespace = get_class($entity);

        switch ($this->getState($entity)) {
            case self::STATE_MANAGED:
                if (!isset($this->update[$namespace])) {
                    $this->update[$namespace] = new SplObjectStorage();
                }
                $this->update[$namespace]->attach($entity);
                break;
            case self::STATE_NEW:
                if (!isset($this->create[$namespace])) {
                    $this->create[$namespace] = new SplObjectStorage();
                }
                $this->create[$namespace]->attach($entity);
                break;
            case self::STATE_REMOVED:
            case self::STATE_DETACHED:
            default:
                throw UnitOfWorkException::forPersistingEntityInInvalidState($entity);
        }
    }

    public function getState(Storable $entity): int
    {
        $oid = spl_object_hash($entity);
        if (isset($this->states[$oid])) {
            return $this->states[$oid];
        }

        $namespace = get_class($entity);

        if (isset($this->remove[$namespace]) && $this->remove[$namespace]->contains($entity)) {
            return $this->states[$oid] = self::STATE_REMOVED;
        }

        if ($this->entityManager->contains($entity)) {
            return $this->states[$oid] = self::STATE_MANAGED;
        }

        try {
            $this->entityManager->getRepository($namespace)->get($entity->getId());
            return $this->states[$oid] = self::STATE_DETACHED;
        } catch (\Exception $exception) {
            return $this->states[$oid] = self::STATE_NEW;
        }
    }

    private function removeOne(Storable $entity): void
    {
        $namespace = get_class($entity);
        $oid = spl_object_hash($entity);

        if (isset($this->states[$oid]) && $this->states[$oid] === self::STATE_MANAGED) {
            $this->states[$oid] = self::STATE_REMOVED;
        }

        if (!isset($this->remove[$namespace])) {
            $this->remove[$namespace] = new SplObjectStorage();
        }

        if (isset($this->create[$namespace])) {
            $this->create[$namespace]->detach($entity);
        }

        if (isset($this->update[$namespace])) {
            $this->update[$namespace]->detach($entity);
        }

        $this->remove[$namespace]->attach($entity);
    }

    public function attach(Storable ...$entities): void
    {
        foreach ($entities as $entity) {
            $this->entityManager->attach($entity);
        }
    }

    public function contains(Storable $entity): bool
    {
        $contains = $this->entityManager->contains($entity);
        $oid = spl_object_hash($entity);
        $this->states[$oid] = self::STATE_MANAGED;

        return $contains;
    }

    public function detach(Storable ...$entities): void
    {
        foreach ($entities as $entity) {
            $this->detachOne($entity);
        }
    }

    private function detachOne(Storable $entity): void
    {
        $this->states[spl_object_hash($entity)] = self::STATE_DETACHED;
        $namespace = get_class($entity);

        if (isset($this->update[$namespace])) {
            $this->update[$namespace]->detach($entity);
        }

        if (isset($this->create[$namespace])) {
            $this->create[$namespace]->detach($entity);
        }

        if (isset($this->remove[$namespace])) {
            $this->remove[$namespace]->detach($entity);
        }

        $this->entityManager->detach($entity);
    }
}
