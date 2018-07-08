<?php declare(strict_types=1);

namespace Igni\Storage\Mapping;

use Countable;
use Iterator;

interface Collection extends Iterator, Countable
{
    public function contains($element): bool;
    public function first();
    public function last();
    public function at(int $index);
    public function toArray(): array;
}
