<?php declare(strict_types=1);

namespace IgniTest\Functional\Storage\Id;

use Igni\Crypto\Base58;
use Igni\Storage\Exception\MappingException;
use Igni\Storage\Id\Uuid;
use PHPUnit\Framework\TestCase;

final class UuidTest extends TestCase
{
    public function testAutoGenerateId(): void
    {
        $uuid = new Uuid();
        self::assertInstanceOf(Uuid::class, $uuid);
        self::assertNotEmpty($uuid->getValue());
        self::assertSame(36, strlen($uuid->getLong()));
        self::assertTrue(strlen($uuid->getShort()) >= 21 && strlen($uuid->getShort()) <= 22);
    }

    public function testInstantiateWithPredefinedValue(): void
    {
        $uuid = new Uuid('01f8c1c0-5d38-4a28-aa84-769fdc4259a9');

        self::assertSame('01f8c1c0-5d38-4a28-aa84-769fdc4259a9', $uuid->getLong());
        self::assertSame('33bdqsTP4y67GBr3sPZNPB', $uuid->getShort());

        $uuid = new Uuid('33bdqsTP4y67GBr3sPZNPB');

        self::assertSame('01f8c1c0-5d38-4a28-aa84-769fdc4259a9', $uuid->getLong());
        self::assertSame('33bdqsTP4y67GBr3sPZNPB', $uuid->getShort());
    }

    public function testFailOnInvalidLongUuid(): void
    {
        $this->expectException(MappingException::class);
        new Uuid('invaliduuid');
    }

    public function testFailOnInvalidShortUuid(): void
    {
        $this->expectException(MappingException::class);
        new Uuid(Base58::encode('invaliduuid'));
    }
}
