<?php

namespace Proengeno\EdifactMapper\Test;

use Apfelfrisch\DataTransferObject\DataTransferObject;
use Apfelfrisch\DataTransferObject\Test\Doubles\BasicDto;
use Apfelfrisch\DataTransferObject\Test\Doubles\CastableDto;
use Apfelfrisch\DataTransferObject\Test\Doubles\ComplexDto;
use DateTime;
use PHPUnit\Framework\TestCase;

class DataTransferObjectTest extends TestCase
{
    /** @test */
    public function it_intanciates_from_an_array()
    {
        $dto = BasicDto::fromArray(['string' => 'three', 'int' => 1, 'float' => 2.2]);

        $this->assertEquals(1, $dto->int);
        $this->assertEquals(2.2, $dto->float);
        $this->assertEquals('three', $dto->string);
    }

    /** @test */
    public function it_return_its_public_properties_as_an_array()
    {
        $dto = BasicDto::fromArray(['float' => 2.2, 'string' => 'three', 'int' => 1]);

        $this->assertEquals(['int' => 1, 'float' => 2.2, 'string' => 'three'], $dto->all());
    }

    /** @test */
    public function it_return_its_only_specfic_public_properties()
    {
        $dto = BasicDto::fromArray(['float' => 2.2, 'string' => 'three', 'int' => 1]);

        $this->assertEquals(['float' => 2.2, 'string' => 'three'], $dto->only('string', 'float')->toArray());
    }

    /** @test */
    public function it_return_its_only_all_public_properties_with_exceptions()
    {
        $dto = BasicDto::fromArray(['float' => 2.2, 'string' => 'three', 'int' => 1]);

        $this->assertEquals(['int' => 1], $dto->except('string', 'float')->toArray());
    }

    /** @test */
    public function it_casts_properties_with_attributes()
    {
        $dto = CastableDto::fromArrayWithCast([
            'date' => '2020-01-01',
            'basicDto' => ['float' => 2.2, 'string' => 'three', 'int' => 1]
        ]);

        $this->assertInstanceOf(DateTime::class, $dto->date);
        $this->assertInstanceOf(BasicDto::class, $dto->basicDto);
    }
}
