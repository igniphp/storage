<?php declare(strict_types=1);

namespace Igni\Storage;

interface Storable
{
    public function getId(): Id;
}
