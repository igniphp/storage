<?php declare(strict_types=1);

namespace IgniTest\Unit\Storage;

use Igni\Storage\EntityManager;
use Igni\Storage\EntityStorage;
use Igni\Utils\TestCase;

class InkTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $entityManager = \Mockery::mock(EntityManager::class);
        $ink = new EntityStorage($entityManager);
        self::assertInstanceOf(EntityStorage::class, $ink);
    }
}
