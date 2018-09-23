<?php declare(strict_types=1);

namespace Igni\Storage\Id;

use Igni\Storage\Id;

class CompositeId implements Id
{
    private $value;

    public function __construct(array $value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return implode('.', array_values($this->value));
    }
}
