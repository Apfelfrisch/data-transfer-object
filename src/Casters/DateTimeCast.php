<?php

namespace Apfelfrisch\DataTransferObject\Casters;

use Apfelfrisch\DataTransferObject\Caster;
use Attribute;
use InvalidArgumentException;
use DateTime;
use DateTimeZone;

#[Attribute(Attribute::TARGET_PROPERTY)]
class DateTimeCast implements Caster
{
    private DateTimeZone $timezone;

    public function __construct(?string $timezone = null)
    {
        $this->timezone = new DateTimeZone($timezone ?? date_default_timezone_get());
    }

    public function __invoke(mixed $property, string $type): DateTime
    {
        if ($property instanceof DateTime) {
            return $property;
        }

        if (is_string($property)) {
            return new DateTime($property, $this->timezone);
        }

        throw new InvalidArgumentException("$property has an Invalid Type (" . gettype($property) . ")");
    }
}
