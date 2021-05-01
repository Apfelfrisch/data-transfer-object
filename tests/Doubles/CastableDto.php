<?php

namespace Apfelfrisch\DataTransferObject\Test\Doubles;

use Apfelfrisch\DataTransferObject\Casters\DtoCast;
use Apfelfrisch\DataTransferObject\Casters\DateTimeCast;
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
