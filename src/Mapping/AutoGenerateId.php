<?php declare(strict_types=1);

namespace Igni\Storage\Mapping;

use Igni\Storage\Id;

/**
 * @property Id $id
 */
trait AutoGenerateId
{
    public function getId(): Id
    {
        if ($this->id === null) {
            $this->id = new Uuid();
        }

        return $this->id;
    }
}
