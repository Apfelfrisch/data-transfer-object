<?php

namespace Apfelfrisch\DataTransferObject\Casters;

use Apfelfrisch\DataTransferObject\Caster;
use Apfelfrisch\DataTransferObject\DataTransferObject;
use Apfelfrisch\DataTransferObject\Reflection;
use Attribute;
use InvalidArgumentException;
use DateTime;

#[Attribute(Attribute::TARGET_PROPERTY)]
class DtoCast implements Caster
{
    public function __invoke(mixed $property, string $type): DataTransferObject
    {
        if (! is_subclass_of($type, DataTransferObject::class)) {
            throw new InvalidArgumentException("$type has to be a Subclass of " . DataTransferObject::class);
        }

        if ($property instanceof DataTransferObject) {
            return $property;
        }

        if (! is_array($property)) {
            throw new InvalidArgumentException("Attributes of DataTransferObject has to be an array");
        }

        /**
         * @var class-string<DataTransferObject> $type
         * @var array<string, mixed> $property
         */
        return $type::fromArrayWithCast($property);
    }
}
