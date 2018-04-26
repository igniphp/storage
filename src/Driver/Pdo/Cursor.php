<?php declare(strict_types=1);

namespace Igni\Storage\Driver\Pdo;

use Igni\Storage\Entity;
use Igni\Storage\Exception\CursorException;
use Igni\Storage\Hydration\ObjectHydrator;
use IteratorIterator;

class Cursor implements \Igni\Storage\Driver\Cursor
{
    /** @var Connection  */
    private $connection;
    /** @var string */
    private $query;
    /** @var array */
    private $params;
    /** @var ObjectHydrator */
    private $hydrator;
    /** @var \PDOStatement */
    private $baseCursor;
    /** @var Entity|array|null */
    private $current = null;
    /** @var  IteratorIterator */
    private $iterator;

    public function __construct(
        Connection $connection,
        string $query,
        array $params = null
    ) {
        $this->connection = $connection;
        $this->query = $query;
        $this->params = $params;
    }

    public function getBaseCursor(): \PDOStatement
    {
        $this->open();
        return $this->baseCursor;
    }

    public function getConnection(): \Igni\Storage\Driver\Connection
    {
        return $this->connection;
    }

    public function setHydrator(ObjectHydrator $hydrator): void
    {
        $this->hydrator = $hydrator;
    }
    public function current()
    {
        $this->open();
        return $this->current;
    }

    public function next(): void
    {
        $this->open();
        $this->iterator->next();
        $this->current = $this->fetch();
    }

    public function key(): int
    {
        $this->open();
        return $this->iterator->key();
    }

    public function valid(): bool
    {
        $this->open();
        return $this->iterator->valid();
    }

    public function rewind(): void
    {
        $this->close();
        $this->open();
    }

    public function close(): void
    {
        $this->current = null;
        if ($this->baseCursor) {
            $this->baseCursor = null;
            $this->iterator = null;
        }
    }

    public function execute(): void
    {
        $this->open();
        $this->close();
    }

    public function open(): void
    {
        if ($this->iterator) {
            return;
        }
        try {
            $this->baseCursor = $this->connection
                ->getBaseConnection()
                ->prepare($this->query);
            $this->baseCursor->setFetchMode(\PDO::FETCH_ASSOC);
            $this->baseCursor->execute($this->params);
            $this->iterator = new IteratorIterator($this->baseCursor);
            $this->iterator->rewind();
            $this->current = $this->fetch();
        } catch (\Exception $e) {
            throw CursorException::forExecutionFailure($this, $e->getMessage());
        }
    }

    public function toArray(): array
    {
        return iterator_to_array($this);
    }

    private function fetch()
    {
        $fetched = $this->iterator->current();
        if ($this->hydrator && $fetched !== null) {
            $fetched = $this->hydrator->hydrate($fetched);
        }

        return $fetched;
    }

    public function __destruct()
    {
        $this->close();
    }
}
