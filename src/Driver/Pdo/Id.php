<?php declare(strict_types=1);

namespace Igni\Storage\Driver\Pdo;

use Igni\Utils\Uuid;

class Id implements \Igni\Storage\Id
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public static function generate(): self
    {
        return new self(Uuid::generateShort());
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
