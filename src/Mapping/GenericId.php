<?php declare(strict_types=1);

namespace Igni\Storage\Mapping;

use Igni\Storage\Id;

class GenericId implements Id
{
    private $value;

    public function __construct($value = null)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->getValue();
    }
}
