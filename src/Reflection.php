<?php

namespace Apfelfrisch\DataTransferObject;

use Apfelfrisch\DataTransferObject\Casting\Cast;
use ReflectionClass;
use ReflectionProperty;
use ReflectionAttribute;

class Reflection
{
    private ReflectionClass $reflectionClass;

    /** @param class-string $dataTransferObject*/
    public function __construct(string $dataTransferObject)
    {
        $this->reflectionClass = new ReflectionClass($dataTransferObject);
    }

    /** @return list<ReflectionProperty> */
    public function getProperties(): array
    {
        return array_values(array_filter(
            $this->reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC),
            fn (ReflectionProperty $property) => ! $property->isStatic()
        ));
    }

    /**
     * @param array<string, mixed> $arrayOfParameters
     *
     * @return array<string, mixed>
     */
    public function castPoperties(array $arrayOfParameters): array
    {
        foreach ($this->getProperties() as $propertiy) {
            if (null === $value = $arrayOfParameters[(string)$propertiy->name] ?? null) {
                continue;
            }

            foreach ($propertiy->getAttributes(Cast::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                $arrayOfParameters[(string)$propertiy->name] = $attribute->newInstance()($value);
                break;
            }
        }

        return $arrayOfParameters;
    }

    /**
     * @param array<string, mixed> $arrayOfParameters
     *
     * @return list<mixed>
     */
    public function sortConstructorParameters(array $arrayOfParameters)
    {
        if (null === $constructor = $this->reflectionClass->getConstructor()) {
            return [];
        }

        if (count($parameters = $constructor->getParameters()) === 0) {
            return [];
        }

        $sortedParameters = [];

        foreach ($parameters as $parameter) {
            $sortedParameters[] = $arrayOfParameters[$parameter->name] ?? null;
        }

        return $sortedParameters;
    }
}
