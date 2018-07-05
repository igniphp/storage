<?php declare(strict_types=1);

namespace Igni\Storage\Id;

use Igni\Storage\Exception\MappingException;
use Igni\Util\UuidGenerator;

class Uuid extends GenericId
{
    private $long;

    public function __construct($value = null)
    {
        if ($value === null) {
            $value = UuidGenerator::generate();
            $this->long = $value;
            return parent::__construct(UuidGenerator::toShort($value));
        }

        $uuid = (string) $value;

        if (!UuidGenerator::validate($uuid)) {
            $uuid = UuidGenerator::fromShort($uuid);
        }

        if (!UuidGenerator::validate($uuid)) {
            throw MappingException::forInvalidUuid($value);
        }

        $this->long = $uuid;
        parent::__construct((string) $value);
    }

    public function getShort(): string
    {
        return $this->getValue();
    }

    public function getLong(): string
    {
        return $this->long;
    }
}
