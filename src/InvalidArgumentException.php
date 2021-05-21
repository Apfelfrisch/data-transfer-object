<?php

namespace Apfelfrisch\DataTransferObject;

use Exception;
use ReflectionClass;
use ReflectionParameter;

class InvalidArgumentException extends \InvalidArgumentException
{
    public static function missingArgument(ReflectionClass $refactionClass, ReflectionParameter $refactionParameter): self
    {
        return new self(
            "Can not instantiate " . $refactionClass->getName() . ", argument \${$refactionParameter->getName()} is missing"
        );
    }
}
