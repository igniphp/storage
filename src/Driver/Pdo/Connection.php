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
    private $host;

    /** @var ConnectionOptions */
    private $options;

    private $queryLog = [];

    public function __construct(string $host, ConnectionOptions $options)
    {
        $this->host = $host;
        $this->options = $options;
    }

    public function close(): void
    {
        $this->handler = null;
    }

    public function open(): void
    {
        if ($this->isOpen()) {
            return;
        }

        $dsn = "{$this->options->getDsn()};host={$this->host}";

        if ($this->options->getDsn() === 'sqlite:') {
            $dsn = "{$this->options->getDsn()}/{$this->host}";
            if ($this->host === 'memory') {
                $dsn = "{$this->options->getDsn()}:{$this->host}";
            }
        }

        $this->handler = new PDO(
            $dsn,
            $this->options->getUsername(),
            $this->options->getPassword(),
            $this->options->getAttributes()
        );
    }

    public function isOpen(): bool
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
        if (!$this->isOpen()) {
            throw ConnectionException::forExecutionFailure('Connection was not open', $this, $parameters);
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
        $this->open();
        return $this->handler;
    }

    public function log(string $query)
    {
        $this->queryLog[] = $query;
    }
}
