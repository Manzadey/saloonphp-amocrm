<?php

declare(strict_types=1);

namespace Manzadey\tests\Modules\CustomField;

use Manzadey\SaloonAmoCrm\Modules\CustomField\CustomFieldModel;
use Manzadey\SaloonAmoCrm\Modules\CustomField\CustomFieldValueModel;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CustomFieldModelTest extends TestCase
{
    public function testReadsValueShapeKeys(): void
    {
        $field = new CustomFieldModel([
            'field_id' => 1,
            'field_name' => 'Phone',
            'field_code' => 'PHONE',
            'field_type' => 'multitext',
        ]);

        $this->assertSame(1, $field->id());
        $this->assertSame('Phone', $field->name());
        $this->assertSame('PHONE', $field->code());
        $this->assertSame('multitext', $field->type());
    }

    public function testReadsReferenceShapeKeys(): void
    {
        $field = new CustomFieldModel([
            'id' => 2,
            'name' => 'Email',
            'code' => 'EMAIL',
            'type' => 'text',
        ]);

        $this->assertSame(2, $field->id());
        $this->assertSame('Email', $field->name());
        $this->assertSame('EMAIL', $field->code());
        $this->assertSame('text', $field->type());
    }

    public function testValueShapeKeysTakePriorityWhenBothPresent(): void
    {
        $field = new CustomFieldModel([
            'field_id' => 1,
            'id' => 2,
        ]);

        $this->assertSame(1, $field->id());
    }

    /**
     * @return array<string, array{CustomFieldValueModel|string|int|float|bool, array<string, mixed>}>
     */
    public static function scalarValues(): array
    {
        return [
            'string' => ['Иванов', ['value' => 'Иванов']],
            'int'    => [42, ['value' => 42]],
            'float'  => [1.5, ['value' => 1.5]],
            'bool'   => [true, ['value' => true]],
            'model'  => [new CustomFieldValueModel(['value' => 'x', 'enum_id' => 7]), ['value' => 'x', 'enum_id' => 7]],
        ];
    }

    /**
     * @param array<string, mixed> $expected
     */
    #[DataProvider('scalarValues')]
    public function testAddValueWrapsScalarsIntoValueShape(
        CustomFieldValueModel|string|int|float|bool $value,
        array $expected,
    ): void {
        $field = (new CustomFieldModel())->addValue($value);

        $this->assertSame([$expected], $field->get('values'));
    }

    public function testAddValueAppendsInsteadOfReplacing(): void
    {
        $field = (new CustomFieldModel())->addValue('первое')->addValue('второе');

        $this->assertSame(
            ['первое', 'второе'],
            array_map(static fn (CustomFieldValueModel $v): ?string => $v->value(), $field->values())
        );
    }

    public function testAddValueIgnoresNull(): void
    {
        $this->assertNull((new CustomFieldModel())->addValue(null)->get('values'));
    }
}
