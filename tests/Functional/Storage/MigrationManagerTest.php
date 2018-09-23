<?php declare(strict_types=1);

namespace Igni\Tests\Functional\Storage;

use Igni\Storage\Migration;
use Igni\Storage\Migration\VersionSynchronizer;
use Igni\Storage\MigrationManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Igni\Storage\Migration\Version;

final class MigrationManagerTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        self::assertInstanceOf(
            MigrationManager::class,
            new MigrationManager($this->createMock(VersionSynchronizer::class))
        );
    }

    /**
     * @param Migration[] $migrations
     * @dataProvider provideMigrations
     */
    public function testRegister(array $migrations): void
    {
        $migrationManager = new MigrationManager($this->createMock(VersionSynchronizer::class));
        foreach ($migrations as $migration) {
            $migrationManager->register($migration);
        }

        self::assertCount(8, self::readAttribute($migrationManager, 'migrations'));
    }

    public function testGetCurrentVersion(): void
    {
        $versionSynchronizerMock = $this->createMock(VersionSynchronizer::class);
        $versionSynchronizerMock->method('getVersion')->willReturn(Version::fromString('0.0.0'));
        $migrationManager = new MigrationManager($versionSynchronizerMock);

        self::assertTrue($migrationManager->getCurrentVersion()->equals(Version::fromString('0.0.0')));
    }

    /**
     * @param Migration[] $migrations
     * @dataProvider provideMigrations
     */
    public function testMigrateUp(array $migrations): void
    {
        $versionSynchronizerMock = $this->createMock(VersionSynchronizer::class);
        $versionSynchronizerMock->method('getVersion')->willReturn(Version::fromString('0.0.0'));
        $migrationManager = new MigrationManager($versionSynchronizerMock);
        foreach ($migrations as $migration) {
            $migrationManager->register($migration);
        }

        $version = $migrationManager->migrate();
        self::assertTrue($version->equalsLiteral('2.0.0'));
    }

    /**
     * @param Migration[] $migrations
     * @dataProvider provideMigrations
     */
    public function testMigrateDown(array $migrations): void
    {
        $version = Version::fromString('2.0.0');
        $versionSynchronizerMock = $this->createMock(VersionSynchronizer::class);
        $versionSynchronizerMock->method('getVersion')->willReturn($version);
        $migrationManager = new MigrationManager($versionSynchronizerMock);
        foreach ($migrations as $migration) {
            $migrationManager->register($migration);
        }
        $migrationManager->register($migration = new class implements Migration {
            public $up = false;
            public $down = false;

            public function up(): void
            {
                $this->up = true;
            }

            public function down(): void
            {
                $this->down = true;
            }

            public function getVersion(): Version
            {
                return Version::fromString('1.9.0');
            }
        });

        $version = $migrationManager->migrate(Version::fromString('1.8.0'));

        self::assertTrue($version->equalsLiteral('1.8.0'));
        self::assertTrue($migration->down);
        self::assertFalse($migration->up);
    }

    public function provideMigrations(): array
    {
        $migrations = [
            '2.0.0' => $this->createMock(Migration::class),
            '1.0.0' => $this->createMock(Migration::class),
            '0.1.0' => $this->createMock(Migration::class),
            '0.1.2' => $this->createMock(Migration::class),
            '1.1.2' => $this->createMock(Migration::class),
            '1.2.2' => $this->createMock(Migration::class),
            '1.0.1' => $this->createMock(Migration::class),
            '0.0.1' => $this->createMock(Migration::class),
        ];

        /**
         * @var string $version
         * @var MockObject|Migration $migration
         */
        foreach ($migrations as $version => $migration) {
            $migration
                ->method('getVersion')
                ->willReturn(Version::fromString($version));
        }

        return [[$migrations]];
    }
}
