<?php

namespace Apfelfrisch\DataTransferObject;

abstract class DataTransferObject
{
    /** @var list<string> */
    protected array $exceptKeys = [];

    /** @var list<string> */
    protected array $onlyKeys = [];

    /**
     * @param array<string, mixed> $parameters
     */
    public static function fromArrayWithCast(array $parameters): static
    {
        $class = new Reflection(static::class);

        return static::fromArray($class->castPoperties($parameters));
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public static function fromArray(array $parameters): static
    {
        $class = new Reflection(static::class);

        return new static(...$class->sortConstructorParameters($parameters));
    }

    /**
     * @param list<array<string, mixed>> $arrayOfParameters
     *
     * @return list<static>
     */
    public static function listFromArray(array $arrayOfParameters): array
    {
        return array_map(
            fn (array $parameters): static => static::fromArray($parameters),
            $arrayOfParameters
        );
    }

    /**
     * @param list<array<string, mixed>> $arrayOfParameters
     *
     * @return list<static>
     */
    public static function listFromArrayWithCast(array $arrayOfParameters): array
    {
        return array_map(
            fn (array $parameters): static => static::fromArrayWithCast($parameters),
            $arrayOfParameters
        );
    }

    public function all(): array
    {
        $class = new Reflection(static::class);

        $data = [];

        foreach ($class->getProperties() as $property) {
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
