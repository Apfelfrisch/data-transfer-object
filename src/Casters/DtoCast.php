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
        if (! (new Reflection($type))->isSubclassOf(DataTransferObject::class)) {
            throw new InvalidArgumentException("$type has to be a Subclass of " . DataTransferObject::class);
        }

        if (! is_array($property)) {
            throw new InvalidArgumentException("Attributes of DataTransferObject has to be an array");
        }

        return $type::fromArrayWithCast($property);
    }
}
