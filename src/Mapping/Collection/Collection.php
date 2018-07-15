<?php declare(strict_types=1);

namespace Igni\Storage\Mapping\Collection;

use Igni\Storage\Exception\CollectionException;
use Iterator;
use Igni\Exception\OutOfBoundsException;

/**
 * Immutable collection representation.
 */
class Collection implements \Igni\Storage\Mapping\Collection
{
    protected $length = 0;
    protected $cursor = 0;
    protected $items = [];

    public function __construct(Iterator $cursor = null)
    {
        if ($cursor === null) {
            $this->items = [];
            $this->length = 0;
            return;
        }

        /** @TODO: Maybe keep cursor as iterator */
        $this->items = iterator_to_array($cursor);
        $this->length = count($this->items);
    }

    /**
     * Reduces a collection to a single value by iteratively combining elements of the collection
     * using the provided callable.
     *
     * @param callable $f($previousValue, $current)
     * @param mixed $initialValue
     * @return mixed
     */
    public function reduce(callable $f, $initialValue = null)
    {
        foreach ($this as $element) {
            $initialValue = $f($initialValue, $element);
        }

        return $initialValue;
    }

    /**
     * Returns new collection with items sorted by the order specified by the compare function.
     *
     * @param callable $compare
     * @return Collection
     */
    public function sort(callable $compare): self
    {
        $collection = clone $this;
        usort($collection->items, $compare);

        return $collection;
    }

    /**
     * Returns new collection with elements that are created by calling f callable on each element.
     *
     * @param callable $f($element)
     * @return Collection
     */
    public function map(callable $f): self
    {
        $collection = new self();
        foreach ($this as $item) {
            $collection->items[] = $f($item);
        }
        $collection->length = $this->length;

        return $collection;
    }

    /**
     * Returns new collection with inserted element(s) at position index.
     *
     * @param int $index
     * @param mixed ...$elements
     * @return Collection
     */
    public function insert(int $index, ...$elements): self
    {
        if ($index < 0 || $index - 1 > $this->length) {
            throw CollectionException::forInvalidIndex($index);
        }

        $collection = clone $this;
        array_splice($collection->items, $index, 0, $elements);

        return $collection;
    }

    /**
     * Returns a new collection with elements extracted as slice.
     *
     * @param int $start
     * @param int $length
     * @return Collection
     */
    public function slice(int $start, int $length): self
    {
        $items = array_slice($this->items, $start, $length);
        $collection = new self();
        $collection->items = $items;
        $collection->length = $length;

        return $collection;
    }

    /**
     * Returns a new Collection with all elements that satisfy the predicate test.
     * @param callable $test($element)
     * @return Collection
     */
    public function where(callable $test): self
    {
        $collection = new self();
        $length = 0;
        foreach ($this as $item) {
            if ($test($item)) {
                $length++;
                $collection->items[] = $item;
            }
        }
        $collection->length = $length;

        return $collection;
    }

    /**
     * Checks whether every element of this iterable satisfies test
     * @param callable $test($element)
     * @return bool
     */
    public function every(callable $test): bool
    {
        foreach($this as $item) {
            if (!$test($item)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks whether any element of this iterable satisfies test
     * @param callable $test($element)
     * @return bool
     */
    public function any(callable $test): bool
    {
        foreach($this as $item) {
            if ($test($item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns instance of the collection with reversed items.
     * @return Collection
     */
    public function reverse(): self
    {
        $collection = clone $this;
        $collection->items = array_reverse($collection->items);

        return $collection;
    }

    /**
     * Removes all objects from this collection; the length of the collection becomes zero.
     */
    public function clear(): void
    {
        $this->items = [];
        $this->length = 0;
        $this->cursor = 0;
    }

    /**
     * Returns new collection with value added to the end of this list, extending the length by one.
     *
     * @param mixed $element
     * @return Collection
     */
    public function add($element): self
    {
        $collection = clone $this;
        $collection->cursor = 0;
        $collection->items[] = $element;
        $collection->length++;

        return $collection;
    }

    public function addMany(...$elements): self
    {
        $collection = clone $this;
        $collection->cursor = 0;
        $collection->items = array_merge($collection->items, $elements);
        $collection->length += count($elements);

        return $collection;
    }

    /**
     * Returns new collection with removed value, decreasing the length by one.
     *
     * @param $element
     * @return Collection
     */
    public function remove($element): self
    {
        $index = array_search($element, $this->items);
        if ($index === false) {
            return $this;
        }

        $collection = clone $this;
        array_splice($collection->items, $index, 1);
        $collection->length--;
        $collection->cursor = 0;

        return $collection;
    }

    public function removeMany(...$elements): self
    {
        $collection = clone $this;
        $collection->items = array_values(array_diff($collection->items, $elements));
        $collection->length -= count($elements);
        $collection->cursor = 0;

        return $collection;
    }

    /**
     * Checks if collection contains given element.
     * @param $element
     * @return bool
     */
    public function contains($element): bool
    {
        return in_array($element, $this->items);
    }

    /**
     * Returns first element of the collection
     * @return mixed
     */
    public function first()
    {
        $this->cursor = 0;

        return $this->at($this->cursor);
    }

    /**
     * Returns last element of the collection
     * @return mixed
     */
    public function last()
    {
        $this->cursor = $this->length - 1;

        return $this->current();
    }

    /**
     * Returns element at the index
     * @param int $offset
     * @return mixed
     */
    public function at(int $offset)
    {
        if ($offset < $this->length) {
            return $this->items[$offset];
        }

        throw new OutOfBoundsException("Invalid offset: ${offset}");
    }

    /**
     * Returns current element
     * @return mixed
     */
    public function current()
    {
        return $this->at($this->cursor);
    }

    /**
     * Moves cursor of the collection forward by one.
     */
    public function next(): void
    {
        $this->cursor++;
    }

    /**
     * Moves cursor of the collection backward by one.
     */
    public function previous(): void
    {
        if ($this->cursor > 0) {
            $this->cursor--;
        }
    }

    public function key(): int
    {
        return $this->cursor;
    }

    public function valid(): bool
    {
        if ($this->cursor < $this->length) {
            return true;
        }

        return false;
    }

    public function rewind(): void
    {
        $this->cursor = 0;
    }

    public function count(): int
    {
        return $this->length;
    }

    public function toArray(): array
    {
        return $this->items;
    }
}
