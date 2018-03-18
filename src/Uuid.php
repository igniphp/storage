<?php declare(strict_types=1);

namespace Igni\Storage;

class Uuid implements Id
{
    private $value;

    public function __construct($value = null)
    {
        $this->value = $value ?? \Igni\Utils\Uuid::generateShort();
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
