<?php

declare(strict_types=1);

namespace Manzadey\tests;

use Manzadey\SaloonAmoCrm\Modules\CustomField\ReferrerFieldModel;
use PHPUnit\Framework\TestCase;

class ReferrerFieldModelTest extends TestCase
{
    public function testDefaultAppliesWhenValueAbsent(): void
    {
        self::assertSame('REFERRER', (new ReferrerFieldModel())->get('field_code'));
    }

    public function testExplicitValueOverridesDefaultAndStaysScalar(): void
    {
        $model = new ReferrerFieldModel(['field_code' => 'CUSTOM']);

        self::assertSame('CUSTOM', $model->get('field_code'));
    }
}
