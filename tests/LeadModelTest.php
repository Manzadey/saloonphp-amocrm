<?php

declare(strict_types=1);

namespace Manzadey\tests;

use Manzadey\SaloonAmoCrm\Contracts\CustomFieldsValuesContract;
use Manzadey\SaloonAmoCrm\Contracts\TagsContract;
use Manzadey\SaloonAmoCrm\Contracts\TaskContract;
use Manzadey\SaloonAmoCrm\Modules\Lead\LeadModel;
use Manzadey\SaloonAmoCrm\Modules\Model;
use PHPUnit\Framework\TestCase;

class LeadModelTest extends TestCase
{
    protected LeadModel $leadModel;

    protected function setUp(): void
    {
        $this->leadModel = new LeadModel();
    }

    public function testImplementsContracts(): void
    {
        $contracts = [
            TagsContract::class,
            TaskContract::class,
            CustomFieldsValuesContract::class,
        ];

        foreach ($contracts as $contract) {
            $this->assertInstanceOf($contract, $this->leadModel);
        }
    }

    public function testModelStoresData(): void
    {
        $model = new LeadModel(['name' => 'Test Lead']);
        $this->assertEquals('Test Lead', $model->get('name'));
    }

    public function testModelMergesDefaults(): void
    {
        $model = new class(['custom' => 'value']) extends Model {
            protected array $defaults = ['default' => 'value'];
        };

        $this->assertEquals('value', $model->get('default'));
        $this->assertEquals('value', $model->get('custom'));
    }

    public function testGetWithDotNotation(): void
    {
        $model = new LeadModel([
            'nested' => [
                'key' => 'value'
            ]
        ]);

        $this->assertEquals('value', $model->get('nested.key'));
    }

    public function testGetWithDefault(): void
    {
        $model = new LeadModel();
        $this->assertEquals('default', $model->get('nonexistent', 'default'));
    }
}