<?php declare(strict_types=1);

namespace IgniTestFunctional\Storage\Driver\MongoDB;

use Igni\Storage\Driver\MongoDB\Id;
use MongoDB\BSON\ObjectId;
use PHPUnit\Framework\TestCase;

final class IdTest extends TestCase
{
    public function testAutoGenerateId(): void
    {
        $instance = new Id();

        self::assertNotEmpty($instance->getValue());
        self::assertInstanceOf(ObjectId::class, $instance->getValue());
        self::assertSame(24, strlen((string) $instance));
    }

    public function testInstantiateWithPredefinedValue(): void
    {
        $instance = new Id('5b473ba77c2df709851ba471');

        self::assertNotEmpty($instance->getValue());
        self::assertInstanceOf(ObjectId::class, $instance->getValue());
        self::assertSame('5b473ba77c2df709851ba471', (string) $instance);
    }
}
