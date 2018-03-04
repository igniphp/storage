<?php declare(strict_types=1);

namespace Igni\Storage\Mapping;

use Igni\Storage\Entity;

interface IdentityMap
{
    public function get(string $class, $id): Entity;
    public function has(string $class, $id): bool;
    public function attach(Entity $entity): Entity;
    public function detach(Entity $entity): Entity;
    public function clear(): void;
}
