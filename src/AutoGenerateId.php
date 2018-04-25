<?php declare(strict_types=1);

namespace Igni\Storage;

trait AutoGenerateId
{
    public function getId(): \Igni\Storage\Id
    {
        if ($this->id === null) {
            $this->id = new Uuid();
        }

        return $this->id;
    }
}
