<?php declare(strict_types=1);

namespace Igni\Tests\Unit\Storage;

use Igni\Storage\Migration\Version;
use PHPUnit\Framework\TestCase;

final class VersionTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        self::assertInstanceOf(Version::class, new Version(1, 0, 0));
    }

    public function testFromString(): void
    {
        $version = Version::fromString('1.2.3');
        self::assertInstanceOf(Version::class, $version);
        self::assertSame(1, $version->getMajor());
        self::assertSame(2, $version->getMinor());
        self::assertSame(3, $version->getPatch());
    }

    public function testGetMajor(): void
    {
        $version = new Version(1, 0,0 );
        self::assertSame(1, $version->getMajor());
    }

    public function testGetMinor(): void
    {
        $version = new Version(0, 1,0 );
        self::assertSame(1, $version->getMinor());
    }

    public function testGetPatch(): void
    {
        $version = new Version(0, 0,1 );
        self::assertSame(1, $version->getPatch());
    }

    public function testGetNextMajor(): void
    {
        $version = new Version(0, 0,0 );
        $next = $version->getNextMajor();
        self::assertNotSame($next, $version);
        self::assertSame('1.0.0', (string) $next);
    }

    public function testGetNextMinor(): void
    {
        $version = new Version(0, 0,0 );
        $next = $version->getNextMinor();
        self::assertNotSame($next, $version);
        self::assertSame('0.1.0', (string) $next);
    }

    public function testGetNextPatch(): void
    {
        $version = new Version(0, 0,0 );
        $next = $version->getNextPatch();
        self::assertNotSame($next, $version);
        self::assertSame('0.0.1', (string) $next);
    }

    public function testGreaterThan(): void
    {
        $version = Version::fromString('0.0.0');
        $equalsVersion = Version::fromString('0.0.0');
        $greaterMinor = Version::fromString('0.1.0');

        self::assertFalse($version->greaterThan($equalsVersion));
        self::assertFalse($version->greaterThan($greaterMinor));
        self::assertTrue($greaterMinor->greaterThan($version));
    }

    public function testGreaterOrEquals(): void
    {
        $version = Version::fromString('0.0.0');
        $equalsVersion = Version::fromString('0.0.0');
        $greaterMinor = Version::fromString('0.1.0');

        self::assertTrue($version->greaterOrEquals($equalsVersion));
        self::assertTrue($greaterMinor->greaterOrEquals($version));
        self::assertFalse($version->greaterOrEquals($greaterMinor));
    }

    public function testLowerThan(): void
    {
        $version = Version::fromString('0.0.0');
        $equalsVersion = Version::fromString('0.0.0');
        $greaterMinor = Version::fromString('0.1.0');

        self::assertFalse($version->lowerThan($equalsVersion));
        self::assertTrue($version->lowerThan($greaterMinor));
        self::assertFalse($greaterMinor->lowerThan($version));
    }

    public function testLowerOrEquals(): void
    {
        $version = Version::fromString('0.0.0');
        $equalsVersion = Version::fromString('0.0.0');
        $greaterMinor = Version::fromString('0.1.0');

        self::assertTrue($version->lowerOrEquals($equalsVersion));
        self::assertTrue($version->lowerOrEquals($greaterMinor));
        self::assertFalse($greaterMinor->lowerOrEquals($version));
    }
}
