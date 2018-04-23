<?php declare(strict_types=1);

namespace Igni\Storage;

use Igni\Storage\Exception\UnitOfWorkException;
use SplObjectStorage;

class EntityStorage implements UnitOfWork, RepositoryContainer
{
    private const STATE_NEW = 1;
    private const STATE_DIRTY = 2;
    private const STATE_REMOVED = 3;
    private const STATE_DETACHED = 4;
    private const ACTION_CREATE = 'create';
    private const ACTION_REMOVE = 'remove';
    private const ACTION_UPDATE = 'update';

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

    private $entityManager;

    public function __construct(EntityManager $manager)
    {
        $this->entityManager = $manager;
    }

    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    public function getRepository(string $entity): Repository
    {
        return $this->entityManager->getRepository($entity);
    }

    public function hasRepository(string $entity): bool
    {
        return $this->entityManager->hasRepository($entity);
    }

    public function get(string $entity, $id): Entity
    {
        return $this->entityManager->get($entity, $id);
    }

    /**
     * @param Entity[] ...$entities
     */
    public function persist(Entity ...$entities): void
    {
        foreach ($entities as $entity) {
            $this->persistOne($entity);
        }
    }

    /**
     * @param Entity[] ...$entities
     */
    public function remove(Entity ...$entities): void
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

    private function persistOne(Entity $entity): void
    {
        $namespace = get_class($entity);

        switch ($this->getState($entity)) {
            case self::STATE_DIRTY:
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

    public function getState(Entity $entity): int
    {
        $namespace = get_class($entity);

        if (isset($this->remove[$namespace]) && $this->remove[$namespace]->contains($entity)) {
            return self::STATE_REMOVED;
        }

        if ($this->entityManager->contains($entity)) {
            return self::STATE_DIRTY;
        }

        try {
            $this->entityManager->getRepository($namespace)->get($entity->getId());
            return self::STATE_DETACHED;
        } catch (\Exception $exception) {
            return self::STATE_NEW;
        }
    }

    private function removeOne(Entity $entity): void
    {
        $namespace = get_class($entity);

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

    public function attach(Entity ...$entities): void
    {
        foreach ($entities as $entity) {
            $this->entityManager->attach($entity);
        }
    }

    public function contains(Entity $entity): bool
    {
        return $this->entityManager->contains($entity);
    }

    public function detach(Entity ...$entities): void
    {
        foreach ($entities as $entity) {
            $this->detachOne($entity);
        }
    }

    private function detachOne(Entity $entity): void
    {
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
