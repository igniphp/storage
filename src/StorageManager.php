<?php declare(strict_types=1);

namespace Igni\Storage;

use Igni\Storage\Driver\Connection;
use Igni\Storage\Driver\ConnectionManager;
use Igni\Storage\Exception\StorageException;

final class StorageManager
{
    private static $connections = [];
    private static $defaultConnection;
    private static $storage;

    private function __construct() {}

    public static function configure()
    {
        self::$storage = $storage;
    }

    public static function addConnection(Connection $connection, string $name = 'default'): void
    {
        ConnectionManager::addConnection($connection, $name);
    }

    public static function hasConnection(string $name): bool
    {
        return ConnectionManager::hasConnection($name);
    }

    public static function hasDefaultConnection(): bool
    {
        return self::$defaultConnection !== null;
    }

    public static function getDefaultConnection(): Connection
    {
        if (!self::hasDefaultConnection()) {
            throw StorageException::forNotRegisteredConnection('default');
        }
        return self::$defaultConnection;
    }

    public static function getConnection(string $name): Connection
    {
        if (!self::hasConnection($name)) {
            throw StorageException::forNotRegisteredConnection($name);
        }

        return self::$connections[$name];
    }

    public static function getRepository(string $entity): Repository
    {

    }

    public static function hasRepository(string $entity): bool
    {

    }

    public static function addRepository(Repository ...$repositories): void
    {

    }

    public static function getEntityManager(): EntityManager
    {

    }
}
