<?php declare(strict_types=1);

namespace Igni\Storage;

use Igni\Storage\Migration\Version;
use Igni\Storage\Migration\VersionSynchronizer;

/**
 * Lightweight migration system usable with multiple databases.
 * Using migration manager one must implement VersionSynchronizer
 * which is responsible for providing current system version as well
 * as storing version used by migration.
 *
 * @see \Igni\Storage\Migration\VersionSynchronizer
 * @package Igni\Storage
 */
class MigrationManager
{
    /**
     * @var VersionSynchronizer
     */
    private $synchronizer;

    /**
     * @var Migration[]
     */
    private $migrations = [];

    public function __construct(VersionSynchronizer $synchronizer)
    {
        $this->synchronizer = $synchronizer;
    }

    public function register(Migration $migration): void
    {
        $this->migrations[] = $migration;
    }

    public function getCurrentVersion(): Version
    {
        return $this->synchronizer->getVersion();
    }

    public function migrate(Version $targetVersion = null): Version
    {
        $lastVersion = $this->synchronizer->getVersion();
        if ($targetVersion === null) {
            $targetVersion = $this->findNewestVersion();
        }

        if ($targetVersion->greaterThan($lastVersion)) {
            foreach ($this->migrations as $migration) {
                if ($migration->getVersion()->lowerOrEquals($targetVersion)) {
                    $migration->up();
                }
            }
        } else if ($targetVersion->lowerThan($lastVersion)) {
            foreach ($this->migrations as $migration) {
                if ($migration->getVersion()->greaterThan($targetVersion)) {
                    $migration->down();
                }
            }
        }

        $this->synchronizer->setVersion($targetVersion);

        return $targetVersion;
    }

    private function findNewestVersion(): Version
    {
        $version = Version::fromString('0.0.0');

        foreach ($this->migrations as $migration) {
            if ($migration->getVersion()->greaterThan($version)) {
                $version = clone $migration->getVersion();
            }
        }

        return $version;
    }
}
