<?php

namespace Apfelfrisch\DataTransferObject;

use Apfelfrisch\DataTransferObject\Casters\DtoCast;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionParameter;
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
            if ($class->getName() === $subclass) {
                return true;
            }
        }

        return false;
    }

    /** @param ReflectionClass|class-string $interface */
    public function implementsInterface(ReflectionClass|string $interface): bool
    {
        return $this->reflectionClass->implementsInterface($interface);
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
     * @return list<ReflectionParameter>
     */
    public function constructorParameters()
    {
        if (null === $constructor = $this->reflectionClass->getConstructor()) {
            return [];
        }

        return $constructor->getParameters();
    }

    /**
     * @param array<string, mixed> $arrayOfParameters
     *
     * @return array<string, mixed>
     */
    public function castToConstructor(array $arrayOfParameters): array
    {
        foreach ($this->getProperties() as $parameter) {
            if (! isset($arrayOfParameters[$parameter->getName()])) {
                continue;
            }

            /** @var mixed */
            $value = $arrayOfParameters[$parameter->getName()];

            if ( ($type = $parameter->getType()) instanceof ReflectionNamedType) {

                if (class_exists($type->getName()) && self::new($type->getName())->isSubclassOf(DataTransferObject::class)) {
                    $arrayOfParameters[$parameter->getName()] = (new DtoCast)($value, $type->getName());
                    continue;
                }

                foreach ($parameter->getAttributes(Caster::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                    $arrayOfParameters[$parameter->getName()] = $attribute->newInstance()(
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
    public function sortToConstructor(array $arrayOfParameters)
    {
        return array_map(fn(ReflectionParameter $parameter): mixed
            => $arrayOfParameters[$parameter->getName()] ?? $parameter->getDefaultValue()
        , $this->constructorParameters());
    }
}
