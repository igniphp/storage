<?php declare(strict_types=1);

namespace Igni\Storage;

interface RepositoryContainer
{
    public function getRepository(string $entity): Repository;
    public function hasRepository(string $entity): bool;
}
