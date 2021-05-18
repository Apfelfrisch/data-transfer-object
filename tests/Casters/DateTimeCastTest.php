<?php

namespace Proengeno\EdifactMapper\Test;

use Apfelfrisch\DataTransferObject\Casters\DateTimeCast;
use Apfelfrisch\DataTransferObject\DataTransferObject;
use DateTime;
use PHPUnit\Framework\TestCase;

class DateTimeCastTest extends TestCase
{
    /** @test */
    public function it_can_parses_to_e_defined_timezone()
    {
        $dto = BerlinTimeZoneDto::fromArrayWithCast(['date' => '2020-01-01']);

        $this->assertEquals('Europe/Berlin', $dto->date->getTimezone()->getName());
        $this->assertEquals('2020-01-01', $dto->date->format('Y-m-d'));
    }

    /** @test */
    public function it_parses_to_e_defined_timezone()
    {
        foreach (['UTC', 'Asia/Barnaul'] as $timezoneString) {
            date_default_timezone_set($timezoneString);

            $dto = DefaultTimeZoneDto::fromArrayWithCast(['date' => '2020-01-01']);

            $this->assertEquals($timezoneString, $dto->date->getTimezone()->getName());
            $this->assertEquals('2020-01-01', $dto->date->format('Y-m-d'));
        }
    }
}

class BerlinTimeZoneDto extends DataTransferObject
{
    public function __construct(
        #[DateTimeCast('Europe/Berlin')]
        public DateTime $date
    ) { }
}

class DefaultTimeZoneDto extends DataTransferObject
{
    public function __construct(
        #[DateTimeCast]
        public DateTime $date
    ) { }
}
