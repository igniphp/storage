<?php declare(strict_types=1);

namespace Igni\Storage\Driver;

interface ConnectionOptions
{
    public function setName(string $name);
    public function getName(): string;
}
