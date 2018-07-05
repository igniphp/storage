<?php declare(strict_types=1);

namespace Igni\Storage\Mapping;

use Igni\Storage\Storable;

interface IdentityMap
{
    public function get(string $class, $id): Storable;
    public function has(string $class, $id): bool;
    public function attach(Storable $entity): Storable;
    public function detach(Storable $entity): Storable;
    public function clear(): void;
}
