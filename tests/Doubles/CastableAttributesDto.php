<?php

namespace Apfelfrisch\DataTransferObject\Test\Doubles;

use DateTime;
use Apfelfrisch\DataTransferObject\Casting\DateTimeCast;
use Apfelfrisch\DataTransferObject\DataTransferObject;

class CastableAttributesDto extends DataTransferObject
{
    public function __construct(
        #[DateTimeCast]
        public DateTime $date
    ) { }
}
