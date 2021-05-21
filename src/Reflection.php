<?php

namespace Apfelfrisch\DataTransferObject;

use Apfelfrisch\DataTransferObject\Casters\DtoCast;
use Apfelfrisch\DataTransferObject\InvalidArgumentException;
use Exception;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionParameter;

class Reflection
{
    /** @var ReflectionClass<object> */
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
    public function constructorParameters(): array
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

                if (is_subclass_of($type->getName(), DataTransferObject::class)) {
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
        return array_map(function(ReflectionParameter $parameter) use ($arrayOfParameters): mixed {
            try {
                return $arrayOfParameters[$parameter->getName()] ?? $parameter->getDefaultValue();
            } catch (Exception $e) {
                throw InvalidArgumentException::missingArgument($this->reflectionClass, $parameter);
            }
        }, $this->constructorParameters());
    }
}
