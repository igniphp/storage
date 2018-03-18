<?php declare(strict_types=1);

namespace Igni\Storage;

interface Id
{
    public function __construct($value);
    public function getValue();
    public function __toString(): string;
}
