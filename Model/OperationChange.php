<?php declare(strict_types=1);

namespace SamJUK\VerboseDBStatus\Model;

class OperationChange
{
    public const INDENT = 6;

    private string $key;
    private string $oldValue;
    private string $newValue;
    
    public function setKey(string $key): OperationChange
    {
        $this->key = $key;
        return $this;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /** @param mixed $data */
    public function setOldValue($data): OperationChange
    {
        $this->oldValue = var_export($data, true);
        return $this;
    }

    public function getOldValue(): string
    {
        return $this->oldValue;
    }

    /** @param mixed $data */
    public function setNewValue($data): OperationChange
    {
        $this->newValue = var_export($data, true);
        return $this;
    }

    public function getNewValue(): string
    {
        return $this->newValue;
    }

    public function __toString(): string
    {
        return sprintf(
            "%s%s: %s -> %s",
            str_repeat(' ', self::INDENT),
            $this->getKey(),
            $this->getOldValue(),
            $this->getNewValue(),
        );
    }
}
