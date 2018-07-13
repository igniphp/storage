<?php declare(strict_types=1);

namespace Igni\Storage\Driver;

use Igni\Storage\Exception\StorageException;

final class ConnectionManager
{
    private static $defaultConnection;
    /** @var Connection[] */
    private static $connections = [];

    public static function release(): void
    {
        foreach (self::$connections as $connection) {
            $connection->close();
        }

        self::$defaultConnection = null;
        self::$connections = [];
    }

    public static function addConnection(Connection $connection, string $name = 'default'): void
    {
        if (!self::hasDefaultConnection()) {
            self::$defaultConnection = $connection;
        }

        if (self::hasConnection($name)) {
            throw StorageException::forAlreadyExistingConnection($name);
        }

        self::$connections[$name] = $connection;
    }

    public static function hasConnection(string $name): bool
    {
        return isset(self::$connections[$name]);
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
}
