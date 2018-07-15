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

    public static function register(Connection $connection, string $name = 'default'): void
    {
        if (!self::hasDefault()) {
            self::$defaultConnection = $connection;
        }

        if (self::has($name)) {
            throw StorageException::forAlreadyExistingConnection($name);
        }

        self::$connections[$name] = $connection;
    }

    public static function has(string $name): bool
    {
        return isset(self::$connections[$name]);
    }

    public static function hasDefault(): bool
    {
        return self::$defaultConnection !== null;
    }

    public static function getDefault(): Connection
    {
        if (!self::hasDefault()) {
            throw StorageException::forNotRegisteredConnection('default');
        }
        return self::$defaultConnection;
    }

    public static function get(string $name): Connection
    {
        if (!self::has($name)) {
            throw StorageException::forNotRegisteredConnection($name);
        }

        return self::$connections[$name];
    }
}
