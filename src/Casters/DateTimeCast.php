<?php

namespace Apfelfrisch\DataTransferObject\Casters;

use Apfelfrisch\DataTransferObject\Caster;
use Attribute;
use InvalidArgumentException;
use DateTime;

#[Attribute(Attribute::TARGET_PARAMETER)]
class DateTimeCast implements Caster
{
    public function __invoke(mixed $property, string $type): DateTime
    {
        if ($property instanceof DateTime) {
            return $property;
        }

        if (is_string($property)) {
            return new DateTime($property);
        }

        throw new InvalidArgumentException("$property has an Invalid Type (" . gettype($property) . ")");
    }
}
