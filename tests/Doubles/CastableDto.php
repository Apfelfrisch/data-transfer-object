<?php

namespace Apfelfrisch\DataTransferObject\Test\Doubles;

use Apfelfrisch\DataTransferObject\Casting\DtoCast;
use Apfelfrisch\DataTransferObject\Casting\DateTimeCast;
use Apfelfrisch\DataTransferObject\DataTransferObject;
use DateTime;

class CastableDto extends DataTransferObject
{
    public function __construct(
        #[DateTimeCast]
        public DateTime $date,
        public BasicDto $basicDto,
    ) { }
}
