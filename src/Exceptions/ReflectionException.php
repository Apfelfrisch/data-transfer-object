<?php

namespace Apfelfrisch\DataTransferObject\Exceptions;

use Exception;
use ReflectionClass;
use ReflectionParameter;

class ReflectionException extends Exception
{
    public static function missingParameterDefaultValue(ReflectionClass $refactionClass, ReflectionParameter $refactionParameter): self
    {
        return new self(
            "Failed to retrieve the default value for Parameter \${$refactionParameter->getName()} in " . $refactionClass->getName()
        );
    }
}
