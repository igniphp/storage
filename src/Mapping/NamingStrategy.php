<?php declare(strict_types=1);

namespace Igni\Storage\Mapping;

interface NamingStrategy
{
    public function map(string $from): string;
    public function addRule(string $from, $to): void;
    public function hasRule(string $from): bool;
    public function removeRule(string $from): void;
}
