<?php

namespace Apfelfrisch\DataTransferObject;

interface Caster
{
    public function __invoke(mixed $property, string $type): mixed;
}
