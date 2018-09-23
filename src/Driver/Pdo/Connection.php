<?php declare(strict_types=1);

namespace Igni\Storage\Driver\Pdo;

use Igni\Storage\Driver\Connection as ConnectionInterface;
use Igni\Storage\Exception\ConnectionException;
use PDO;

final class Connection implements ConnectionInterface
{
    /** @var PDO|null */
    private $handler;

    /** @var string */
    private $dsn;

    /** @var ConnectionOptions */
    private $options;

    /** @var array */
    private $queryLog = [];

    public function __construct(string $dsn, ConnectionOptions $options = null)
    {
        $this->dsn = $dsn;
        $this->options = $options ?? new ConnectionOptions();
    }

    public function close(): void
    {
        $this->handler = null;
    }

    public function connect(): void
    {
        if ($this->isConnected()) {
            return;
        }

        $this->handler = new PDO(
            $this->dsn,
            $this->options->getUsername(),
            $this->options->getPassword(),
            $this->options->getAttributes()
        );
    }

    public function isConnected(): bool
    {
        return $this->handler !== null;
    }

    /**
     * @param string $query
     * @param array $parameters
     * @return Cursor
     * @throws ConnectionException
     */
    public function execute(...$parameters): Cursor
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        $query = $parameters[0];

        if (isset($parameters[1]) && is_array($parameters[1])) {
            return new Cursor($this, $query, $parameters[1]);
        }

        return new Cursor($this, $query);
    }

    public function beginTransaction(): bool
    {
        return $this->handler->beginTransaction();
    }

    public function inTransaction(): bool
    {
        return $this->handler->inTransaction();
    }

    public function commit(): bool
    {
        return $this->handler->commit();
    }

    public function rollBack(): bool
    {
        return $this->handler->rollBack();
    }

    public function quote($string): string
    {
        return $this->handler->quote((string) $string);
    }

    public function getBaseConnection(): PDO
    {
        $this->connect();
        return $this->handler;
    }

    public function log(string $query)
    {
        $this->queryLog[] = $query;
    }
}
