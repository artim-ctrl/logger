<?php

declare(strict_types = 1);

namespace Artim\Logger\Job;

use Illuminate\Queue\SerializesAndRestoresModelIdentifiers;
use ReflectionClass;

trait LogToken
{
    use SerializesAndRestoresModelIdentifiers;

    /**
     * @return array<string, mixed>
     */
    public function __serialize(): array
    {
        $this->token = get_log_token();

        $values = [];

        $properties = (new ReflectionClass($this))->getProperties();

        $class = get_class($this);

        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }

            if (! $property->isInitialized($this)) {
                continue;
            }

            $value = $property->getValue($this);

            if ($property->hasDefaultValue() && $value === $property->getDefaultValue()) {
                continue;
            }

            $name = $property->getName();

            if ($property->isPrivate()) {
                $name = "\0{$class}\0{$name}";
            } elseif ($property->isProtected()) {
                $name = "\0*\0{$name}";
            }

            $values[$name] = $this->getSerializedPropertyValue($value);
        }

        return $values;
    }

    /**
     * Restore the model after serialization.
     *
     * @param array<string, mixed> $values
     * @return void
     */
    public function __unserialize(array $values): void
    {
        $properties = (new ReflectionClass($this))->getProperties();

        $class = get_class($this);

        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $name = $property->getName();

            if ($property->isPrivate()) {
                $name = "\0{$class}\0{$name}";
            } elseif ($property->isProtected()) {
                $name = "\0*\0{$name}";
            }

            if (! array_key_exists($name, $values)) {
                continue;
            }

            $property->setValue(
                $this,
                $this->getRestoredPropertyValue($values[$name])
            );
        }

        set_log_token($this->token);
    }
}
