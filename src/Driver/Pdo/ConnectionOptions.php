<?php declare(strict_types=1);

namespace Igni\Storage\Driver\Pdo;

use PDO;

final class ConnectionOptions
{
    private $username;
    private $databaseType;
    private $databaseName;
    private $password;
    private $attributes = [];

    public function __construct(string $databaseType, string $databaseName = '', string $username = '', string $password = '')
    {
        $this->username = $username;
        $this->password = $password;
        $this->databaseType = $databaseType;
        $this->databaseName = $databaseName;

        $this->usePersistentConnection();
        $this->useExceptions();
    }

    public function silenceErrors(): void
    {
        $this->attributes[PDO::ATTR_ERRMODE] = PDO::ERRMODE_SILENT;
    }

    public function useExceptions($use = true): void
    {
        if ($use) {
            $this->attributes[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
        } else {
            $this->attributes[PDO::ATTR_ERRMODE] = PDO::ERRMODE_WARNING;
        }
    }

    public function usePersistentConnection($use = true): void
    {
        $this->attributes[PDO::ATTR_PERSISTENT] = $use;
    }

    public function isPersistentConnection(): bool
    {
        return isset($this->attributes[PDO::ATTR_PERSISTENT]) && $this->attributes[PDO::ATTR_PERSISTENT];
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getDsn(): string
    {
        if ($this->databaseName) {
            return "{$this->databaseType}:dbname={$this->databaseName}";
        }

        return "{$this->databaseType}:";
    }
}
