<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Collection;

use Igni\Storage\Driver\Cursor;
use Igni\Storage\Exception\CollectionException;

/**
 * Lazy collection is used for memory saving
 * @package Igni\Storage\Mapping\Collection
 */
class LazyCollection implements \Igni\Storage\Mapping\Collection
{
    protected $cursor;
    protected $length;
    protected $current;
    protected $items = [];
    protected $pointer = 0;
    protected $complete = false;

    public function __construct(Cursor $cursor)
    {
        $this->cursor = $cursor;
    }

    public function contains($element): bool
    {
        foreach ($this as $element) {
            if ($element === $element) {
                return true;
            }
        }

        return false;
    }

    public function first()
    {
        $this->pointer = 0;
        if ($this->pointer < $this->length) {
            return $this->current = $this->items[$this->pointer];
        }
        return $this->current();
    }

    public function last()
    {
        while ($this->valid()) {
            $this->current();
            $this->next();
        }

        return $this->current();
    }

    public function at(int $index)
    {
        $item = null;
        if ($index < $this->length) {
            return $this->current = $this->items[$index];
        }

        if ($this->complete) {
            throw CollectionException::forOutOfBoundsIndex($index);
        }

        $savedPointer = $this->pointer;
        while ($this->pointer <= $index) {
            $item = $this->current();
            if (!$this->valid()) {
                break;
            }
            $this->next();
        }

        if ($this->pointer < $index && $this->complete) {
            $offset = $this->pointer;
            $this->pointer = $savedPointer;
            throw CollectionException::forOutOfBoundsIndex($offset);
        }

        $this->pointer = $savedPointer;

        return $item;
    }

    public function current()
    {
        // Empty result-set or out of bounds.
        if ($this->cursor === null &&
            $this->current === null &&
            ($this->pointer > $this->length || ($this->length === null && $this->complete))
        ) {
            throw CollectionException::forOutOfBoundsIndex($this->pointer);
        }

        if (null === $this->current) {
            if ($this->pointer < $this->length) {
                $this->current = $this->items[$this->pointer];
            } else {
                $this->items[$this->pointer] = $this->current = $this->cursor->current();
            }
        }

        return $this->current;
    }

    public function next(): void
    {
        $this->pointer++;

        if ($this->pointer < $this->length) {
            $this->current = $this->items[$this->pointer];
        } else if ($this->cursor) {
            if ($this->pointer > $this->length) {
                $this->length = $this->pointer;
            }
            $this->cursor->next();
            if ($this->cursor->valid()) {
                $this->items[$this->pointer] = $this->current = $this->cursor->current();
            }
        }
    }

    public function previous(): void
    {
        if ($this->pointer > 0) {
            $this->pointer--;
        }
    }

    public function key()
    {
        if (!$this->current) {
            $this->current();
        }
        return $this->pointer;
    }

    public function index(): int
    {
        return $this->pointer;
    }

    public function valid(): bool
    {
        if ($this->pointer < $this->length) {
            return true;
        }

        if ($this->cursor) {
            $valid = $this->cursor->valid();

            if (!$valid) {
                $this->complete = true;

                $this->cursor->close();
                $this->cursor = null;
            }

            return $valid;
        }

        return false;
    }

    public function rewind(): void
    {
        $this->pointer = 0;
        $this->current = null;
    }

    public function count(): int
    {
        if ($this->complete) {
            return $this->length;
        }
        $this->last();

        return $this->length;
    }

    public function toArray(): array
    {
        if ($this->complete) {
            return $this->items;
        }
        $this->last();

        return $this->items;
    }
}
