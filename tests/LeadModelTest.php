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
        $this->leadModel->set([
            'nested' => [
                'key' => 'value'
            ]
        ]);

        $this->assertEquals('value', $this->leadModel->get('nested.key'));
    }

    public function testGetWithDefault(): void
    {
        $this->assertEquals('default', $this->leadModel->get('nonexistent', 'default'));
    }

    public function testId(): void
    {
        $this->assertNull($this->leadModel->id());
        $this->assertSame($this->leadModel, $this->leadModel->setId(123));
        $this->assertEquals(123, $this->leadModel->id());
    }

    public function testLink(): void
    {
        $this->assertNull($this->leadModel->link());

        $link = 'https://example.com/leads/123';

        $this->leadModel->set([
            '_links' => [
                'self' => [
                    'href' => $link
                ]
            ]
        ]);
        $this->assertEquals($link, $this->leadModel->link());

    }
}