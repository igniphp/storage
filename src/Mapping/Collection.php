<?php declare(strict_types=1);

namespace Igni\Storage\Mapping;

use Iterator;
use Countable;
use Igni\Exception\OutOfBoundsException;
use Igni\Utils\ArrayUtil;

class Collection implements Iterator, Countable
{
    protected $length = 0;
    protected $current;
    protected $index;
    protected $persisted;
    protected $items = [];
    protected $removed = [];
    protected $added = [];

    public function __construct(Iterator $cursor)
    {
        $this->items = iterator_to_array($cursor);
        $this->persisted = $this->items;
        $this->length = count($this->items);
        $this->index = 0;
    }

    public function add($item): bool
    {
        $this->added[] = $item;
        $this->items[] = $item;
        $this->length++;
        $this->index = 0;

        return true;
    }

    public function remove($item): bool
    {
        if (!$this->contains($item)) {
            return false;
        }

        $this->removed[] = $item;
        ArrayUtil::remove($this->items, $item);
        $this->length = count($this->items);
        $this->index = 0;

        return true;
    }

    public function contains($item): bool
    {
        return in_array($item, $this->items);
    }

    public function clear(): void
    {
        $this->items = [];
        $this->length = 0;
    }

    public function reset(): void
    {
        $this->items = $this->persisted;
        $this->length = count($this->items);
        $this->index = 0;
        $this->added = [];
        $this->removed = [];
    }

    public function first()
    {
        $this->index = 0;

        return $this->at($this->index);
    }

    public function last()
    {
        $this->index = $this->length - 1;

        return $this->current();
    }

    public function at(int $offset)
    {
        if ($offset < $this->length) {
            return $this->items[$offset];
        }

        throw new OutOfBoundsException("Invalid offset: ${offset}");
    }

    public function current()
    {
        return $this->at($this->index);
    }

    public function next(): void
    {
        $this->index++;
    }

    public function previous(): void
    {
        if ($this->index > 0) {
            $this->index--;
        }
    }

    public function key(): int
    {
        return $this->index;
    }

    public function valid(): bool
    {
        if ($this->index < $this->length) {
            return true;
        }

        return false;
    }

    public function rewind(): void
    {
        $this->index = 0;
    }

    public function count(): int
    {
        return $this->length;
    }
}
