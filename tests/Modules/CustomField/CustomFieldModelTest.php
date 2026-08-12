<?php

declare(strict_types=1);

namespace Manzadey\tests\Modules\CustomField;

use Manzadey\SaloonAmoCrm\Modules\CustomField\CustomFieldModel;
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
}
