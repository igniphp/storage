<?php declare(strict_types=1);

namespace Igni\Storage;

interface Id
{
    public function __toString(): string;
    public function getValue();
}
