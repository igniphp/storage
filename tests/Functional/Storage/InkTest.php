<?php declare(strict_types=1);

namespace IgniTest\Functional\Storage;

use Igni\Storage\EntityManager;
use Igni\Storage\Ink;
use Igni\Utils\TestCase;

final class InkTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $ink = new Ink(new EntityManager());

        self::assertInstanceOf(Ink::class, $ink);
    }
}
