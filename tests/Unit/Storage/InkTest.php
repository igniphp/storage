<?php declare(strict_types=1);

namespace IgniTest\Unit\Storage;

use Igni\Storage\Driver\EntityManager;
use Igni\Storage\Ink;
use Igni\Utils\TestCase;

class InkTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $entityManager = \Mockery::mock(EntityManager::class);
        $ink = new Ink($entityManager);
        self::assertInstanceOf(Ink::class, $ink);
    }
}
