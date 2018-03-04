<?php declare(strict_types=1);

namespace Igni\Storage\Driver\MongoDB;

use MongoDB\Driver\Command;
use Igni\Storage\Driver\Connection;
use Igni\Storage\Driver\Cursor as CursorInterface;
use Igni\Storage\Driver\MongoDB\Connection as MongoDBConnection;
use Igni\Storage\Entity;
use Igni\Storage\Exception\CursorException;
use Igni\Storage\Hydration\ObjectHydrator;
use IteratorIterator;

class Cursor implements CursorInterface
{
    /** @var MongoDBConnection  */
    private $connection;
    /** @var Command */
    private $command;
    /** @var ObjectHydrator */
    private $hydrator;
    /** @var \MongoDB\Driver\Cursor */
    private $baseCursor;
    /** @var ConnectionOptions */
    private $options;
    /** @var Entity|array|null */
    private $current = null;
    /** @var  IteratorIterator */
    private $iterator;

    public function __construct(
        MongoDBConnection $connection,
        ConnectionOptions $options,
        Command $command
    ) {
        $this->connection = $connection;
        $this->command = $command;
        $this->options = $options;
    }

    public function getId(): string
    {
        return (string) $this->baseCursor->getId();
    }

    public function getBaseCursor(): \MongoDB\Driver\Cursor
    {
        $this->open();
        return $this->baseCursor;
    }

    public function getConnection(): Connection
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

    public function toArray(): array
    {
        return iterator_to_array($this);
    }

    public function open(): void
    {
        if ($this->iterator) {
            return;
        }
        try {
            $this->baseCursor = $this->connection
                ->getBaseConnection()
                ->executeCommand(
                    $this->options->getDatabase(),
                    $this->command
                );
            $this->baseCursor->setTypeMap(['root' => 'array', 'document' => 'array', 'array' => 'array']);
            $this->iterator = new IteratorIterator($this->baseCursor);
            $this->iterator->rewind();
            if ($this->iterator->valid()) {
                $this->current = $this->fetch();
            }
        } catch (\Exception $e) {
            throw CursorException::forExecutionFailure($this, $e->getMessage());
        }
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
