<?php

namespace Apfelfrisch\DataTransferObject;

use ReflectionClass;
use ReflectionProperty;

abstract class DataTransferObject
{
    /** @var list<string> */
    protected array $exceptKeys = [];

    /** @var list<string> */
    protected array $onlyKeys = [];

    public static function fromArray(array $arrayOfParameters): static
    {
        $class = new ReflectionClass(static::class);

        if (null === $constructor = $class->getConstructor()) {
            return new static;
        }

        if (count($parameters = $constructor->getParameters()) === 0) {
            return new static;
        }

        $sortedParameters = [];

        foreach ($parameters as $parameter) {
            /** @var list<mixed> */
            $sortedParameters[$parameter->getPosition()] = $arrayOfParameters[$parameter->name] ?? null;
        }

        return new static(...$sortedParameters);
    }

    public function all(): array
    {
        $class = new ReflectionClass(static::class);

        $properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);

        $data = [];

        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }

            /** @var array<string, mixed> */
            $data[$property->getName()] = $property->getValue($this);
        }

        return $data;
    }

    public function only(string ...$keys): static
    {
        $dataTransferObject = clone $this;

        $dataTransferObject->onlyKeys = [...$this->onlyKeys, ...array_values($keys)];

        return $dataTransferObject;
    }

    public function except(string ...$keys): static
    {
        $dataTransferObject = clone $this;

        $dataTransferObject->exceptKeys = [...$this->exceptKeys, ...array_values($keys)];

        return $dataTransferObject;
    }

    public function toArray(): array
    {
        if (count($this->onlyKeys)) {
            $array = Arr::only($this->all(), $this->onlyKeys);
        } else {
            $array = Arr::except($this->all(), $this->exceptKeys);
        }

        $array = $this->parseArray($array);

        return $array;
    }

    protected function parseArray(array $array): array
    {
        foreach ($array as $key => $value) {
            if ($value instanceof DataTransferObject) {
                $array[$key] = $value->toArray();

                continue;
            }

            if (! is_array($value)) {
                continue;
            }

            $array[$key] = $this->parseArray($value);
        }

        return $array;
    }
}
