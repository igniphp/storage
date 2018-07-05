<?php declare(strict_types=1);

use Igni\Storage\Id\GenericId;
use Igni\Storage\Storable;

class GenericEntity implements Storable, ArrayAccess
{
    private $id;
    private $idKey;
    private $data;

    public function __construct(array $data, string $idKey = 'id')
    {
        $this->idKey = $idKey;

        $id = $data[$this->idKey] ?? null;
        $this->id = new GenericId($id);
        $this->data = $data;
    }

    public function getId(): \Igni\Storage\Id
    {
        return $this->id;
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset] ?? null;
    }

    public function offsetSet($offset, $value)
    {
        if ($offset === $this->idKey) {
            return;
        }

        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if ($offset === $this->idKey) {
            return;
        }

        unset($this->data[$offset]);
    }
}
