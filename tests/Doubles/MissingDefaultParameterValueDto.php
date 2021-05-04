<?php

namespace Apfelfrisch\DataTransferObject\Test\Doubles;

use Apfelfrisch\DataTransferObject\DataTransferObject;

class MissingDefaultParameterValueDto extends DataTransferObject
{
    public function __construct(
        public ?string $int,
    ) { }
}
