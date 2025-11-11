<?php

declare(strict_types=1);

namespace Manzadey\tests;

use Manzadey\SaloonAmoCrm\Modules\Contact\Requests\HasContacts;
use Manzadey\SaloonAmoCrm\Modules\CustomField\Requests\HasCustomFieldsValues;
use Manzadey\SaloonAmoCrm\Modules\Lead\LeadModel;
use Manzadey\SaloonAmoCrm\Modules\Model;
use Manzadey\SaloonAmoCrm\Modules\Tag\Requests\HasTags;
use PHPUnit\Framework\TestCase;

class LeadModelTest extends TestCase
{
    protected LeadModel $leadModel;

    protected function setUp(): void
    {
        $this->leadModel = new LeadModel();
    }

    public function testHasTagsTrait(): void
    {
        $this->assertContains(HasTags::class, class_uses($this->leadModel));
    }

    public function testHasContactsTrait(): void
    {
        $this->assertContains(HasContacts::class, class_uses($this->leadModel));
    }

    public function testHasCustomFieldsValuesTrait(): void
    {
        $this->assertContains(HasCustomFieldsValues::class, class_uses($this->leadModel));
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