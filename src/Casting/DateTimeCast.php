<?php

namespace Apfelfrisch\DataTransferObject\Casting;

use Attribute;
use InvalidArgumentException;
use DateTime;

#[Attribute(Attribute::TARGET_PROPERTY)]
class DateTimeCast implements Cast
{
    public function __invoke(mixed $property): DateTime
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
