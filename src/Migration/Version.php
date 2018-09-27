<?php declare(strict_types=1);

namespace Igni\Storage\Migration;

use Igni\Storage\Exception\VersionException;

/**
 * Value object used by MigrationManager and VersionSynchronizer.
 *
 * @see \Igni\Storage\MigrationManager
 * @see \Igni\Storage\Migration\VersionSynchronizer
 *
 * @package Igni\Storage\Migration
 */
final class Version
{
    private $major;
    private $minor;
    private $patch;

    public function __construct(int $major, int $minor, int $patch)
    {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
    }

    public function getMajor(): int
    {
        return $this->major;
    }

    public function getMinor(): int
    {
        return $this->minor;
    }

    public function getPatch(): int
    {
        return $this->patch;
    }

    public function compare(Version $version): int
    {
        if ($version->major > $this->major) {
            return 1;
        } else if ($this->major > $version->major) {
            return -1;
        } else if ($version->minor > $this->minor) {
            return 1;
        } else if ($this->minor > $version->minor) {
            return -1;
        } else if ($version->patch > $this->patch) {
            return 1;
        } else if ($this->patch > $version->patch) {
            return -1;
        }

        return 0;
    }

    public function lowerThan(Version $version): bool
    {
        return $this->compare($version) === 1;
    }

    public function lowerOrEquals(Version $version): bool
    {
        return $this->compare($version) >= 0;
    }

    public function greaterThan(Version $version): bool
    {
        return $this->compare($version) === -1;
    }

    public function greaterOrEquals(Version $version): bool
    {
        return $this->compare($version) <= 0;
    }

    public function equals(Version $version): bool
    {
        return $this->compare($version) === 0;
    }

    public function equalsLiteral(string $version): bool
    {
        return (string) $this === $version;
    }

    public function getNextPatch(): self
    {
        $instance = clone $this;
        $instance->patch++;

        return $instance;
    }

    public function getNextMinor(): self
    {
        $instance = clone $this;
        $instance->minor++;

        return $instance;
    }

    public function getNextMajor(): self
    {
        $instance = clone $this;
        $instance->major++;

        return $instance;
    }

    public static function fromString(string $version): self
    {
        $version = explode('.', $version);
        if (count($version) !== 3) {
            throw VersionException::forParseError(implode('.', $version));
        }

        return new self((int) $version[0], (int) $version[1], (int) $version[2]);
    }

    public function __toString(): string
    {
        return "{$this->major}.{$this->minor}.{$this->patch}";
    }
}
