<?php

namespace Apfelfrisch\DataTransferObject\Casting;

interface Cast
{
    public function __invoke(mixed $property): mixed;
}
