<?php declare(strict_types=1);

namespace Igni\Storage\Id;

use Igni\Storage\Id;

class GenericId implements Id
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
