<?php declare(strict_types=1);

namespace Igni\Storage\Driver;

use Igni\Storage\Exception\StorageException;

final class ConnectionManager
{
    /** @var Connection[] */
    private static $connections = [];

    private const DEFAULT_NAME = 'default';

    public static function release(): void
    {
        foreach (self::$connections as $connection) {
            $connection->close();
        }

        self::$connections = [];
    }

    public static function register(string $name, Connection $connection): void
    {
        if (self::has($name)) {
            throw StorageException::forAlreadyExistingConnection($name);
        }

        self::$connections[$name] = $connection;
    }

    public static function registerDefault(Connection $connection): void
    {
        self::register(self::DEFAULT_NAME, $connection);
    }

    public static function has(string $name): bool
    {
        return isset(self::$connections[$name]);
    }

    public static function hasDefault(): bool
    {
        return self::has(self::DEFAULT_NAME);
    }

    public static function getDefault(): Connection
    {
        return self::get(self::DEFAULT_NAME);
    }

    public static function get(string $name): Connection
    {
        if (!self::has($name)) {
            throw StorageException::forNotRegisteredConnection($name);
        }

        return self::$connections[$name];
    }
}
