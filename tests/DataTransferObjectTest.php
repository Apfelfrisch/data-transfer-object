<?php

namespace Proengeno\EdifactMapper\Test;

use Apfelfrisch\DataTransferObject\DataTransferObject;
use Apfelfrisch\DataTransferObject\InvalidArgumentException;
use Apfelfrisch\DataTransferObject\Test\Doubles\BasicDto;
use Apfelfrisch\DataTransferObject\Test\Doubles\CastableDto;
use Apfelfrisch\DataTransferObject\Test\Doubles\ComplexDto;
use Apfelfrisch\DataTransferObject\Test\Doubles\MissingDefaultParameterValueDto;
use Apfelfrisch\DataTransferObject\Test\Doubles\NestedDto;
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
    public function it_intanciates_from_an_array_and_casts_it_parameter_with_attributes()
    {
        $dto = CastableDto::fromArrayWithCast([
            'string' => 'Hello',
            'date' => '2020-01-01',
            'basicDto' => ['float' => 2.2, 'string' => 'three', 'int' => 1]
        ]);

        $this->assertIsString($dto->string);
        $this->assertInstanceOf(DateTime::class, $dto->date);
        $this->assertInstanceOf(BasicDto::class, $dto->basicDto);
    }

    /** @test */
    public function it_intanciates_from_an_nested_array_and_casts_it_parameter_with_attributes()
    {
        $dto = NestedDto::fromArrayWithCast([
            'string' => 'Hello',
            'castableDto' => [
                'string' => 'Hello',
                'date' => '2020-01-01',
                'basicDto' => [
                    'float' => 2.2, 'string' => 'three', 'int' => 1
                ]
            ]
        ]);

        $this->assertIsString($dto->string);
        $this->assertInstanceOf(CastableDto::class, $dto->castableDto);
        $this->assertInstanceOf(BasicDto::class, $dto->castableDto->basicDto);
    }

    /** @test */
    public function it_makes_a_ist_of_dtos_from_an_array()
    {
        $list = BasicDto::listFromArray([
            ['string' => 'one', 'int' => 1, 'float' => 0.0],
            ['string' => 'two', 'int' => 1, 'float' => 0.0],
        ]);

        $this->assertCount(2, $list);

        $this->assertEquals('one', $list[0]->string);
        $this->assertEquals('two', $list[1]->string);
    }

    /** @test */
    public function it_makes_a_ist_of_dtos_from_an_array_and_casts_it_parameter_with_attributes()
    {
        $list = CastableDto::listFromArrayWithCast([
            [
                'string' => 'Hello',
                'date' => '2020-01-01',
                'basicDto' => ['float' => 0.0, 'string' => 'one', 'int' => 1]
            ],
            [
                'string' => 'Hello',
                'date' => '2020-02-01',
                'basicDto' => ['float' => 0.0, 'string' => 'two', 'int' => 1]
            ],
        ]);

        $this->assertCount(2, $list);

        $this->assertIsString($list[0]->string);
        $this->assertInstanceOf(DateTime::class, $list[0]->date);
        $this->assertInstanceOf(BasicDto::class, $list[0]->basicDto);

        $this->assertIsString($list[0]->string);
        $this->assertInstanceOf(DateTime::class, $list[1]->date);
        $this->assertInstanceOf(BasicDto::class, $list[1]->basicDto);
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
    public function it_throws_in_exception_if_the_dto_cannot_instantiate()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Can not instantiate Apfelfrisch\DataTransferObject\Test\Doubles\MissingDefaultParameterValueDto, argument $int is missing');

        $dto = MissingDefaultParameterValueDto::fromArray([]);
    }
}
