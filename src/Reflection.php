<?php

namespace Apfelfrisch\DataTransferObject;

use Apfelfrisch\DataTransferObject\Casters\DtoCast;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use InvalidArgumentException;

class Reflection
{
    private ReflectionClass $reflectionClass;

    public function __construct(string $classString)
    {
        if (class_exists($classString)) {
            $this->reflectionClass = new ReflectionClass($classString);
        } else {
            throw new InvalidArgumentException('Class "' . $classString . '" does not exist');
        }
    }

    public static function new(string $classString): self
    {
        return new self($classString);
    }

    public function isSubclassOf(string $subclass): bool
    {
        $class = $this->reflectionClass;

        while(($class = $class->getParentClass()) instanceof ReflectionClass) {
            if ($class->name === $subclass) {
                return true;
            }
        }

        return false;
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
            if (null === $value = $arrayOfParameters[$propertiy->getName()] ?? null) {
                continue;
            }


            if ( ($type = $propertiy->getType()) instanceof ReflectionNamedType) {

                if (class_exists($type->getName()) && self::new($type->getName())->isSubclassOf(DataTransferObject::class)) {
                    $arrayOfParameters[$propertiy->getName()] = (new DtoCast)($value, $type->getName());
                    continue;
                }

                foreach ($propertiy->getAttributes(Caster::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                    $arrayOfParameters[$propertiy->getName()] = $attribute->newInstance()(
                        $value, $type->getName()
                    );
                    break;
                }
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
