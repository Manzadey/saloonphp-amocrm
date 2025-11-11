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

    public function testRequestId(): void
    {
        $this->assertNull($this->leadModel->requestId());

        $this->leadModel->set([
            'request_id' => '1234567890'
        ]);

        $this->assertEquals('1234567890', $this->leadModel->requestId());
    }

    public function testName(): void
    {
        $this->assertNull($this->leadModel->name());
        $this->assertSame($this->leadModel, $this->leadModel->setName('Test Lead'));
        $this->assertEquals('Test Lead', $this->leadModel->name());
        $this->assertSame($this->leadModel, $this->leadModel->removeName());
        $this->assertNull($this->leadModel->name());
    }

    public function testPrice(): void
    {
        $this->assertNull($this->leadModel->price());
        $this->assertSame($this->leadModel, $this->leadModel->setPrice(1000));
        $this->assertEquals(1000, $this->leadModel->price());
        $this->assertSame($this->leadModel, $this->leadModel->removePrice());
        $this->assertNull($this->leadModel->price());
    }

    public function testPipelineId(): void
    {
        $this->assertNull($this->leadModel->pipelineId());
        $this->assertSame($this->leadModel, $this->leadModel->setPipelineId(123));
        $this->assertEquals(123, $this->leadModel->pipelineId());
        $this->assertSame($this->leadModel, $this->leadModel->removePipelineId());
        $this->assertNull($this->leadModel->pipelineId());
    }

    public function testResponsibleUserId(): void
    {
        $this->assertNull($this->leadModel->responsibleUserId());
        $this->assertSame($this->leadModel, $this->leadModel->setResponsibleUserId(123));
        $this->assertEquals(123, $this->leadModel->responsibleUserId());
        $this->assertSame($this->leadModel, $this->leadModel->removeResponsibleUserId());
        $this->assertNull($this->leadModel->responsibleUserId());
    }

    public function testGroupId(): void
    {
        $this->assertNull($this->leadModel->groupId());

        $this->leadModel->set([
            'group_id' => 123
        ]);
        $this->assertEquals(123, $this->leadModel->groupId());
    }

    public function testStatusId(): void
    {
        $this->assertNull($this->leadModel->statusId());
        $this->assertSame($this->leadModel, $this->leadModel->setStatusId(123));
        $this->assertEquals(123, $this->leadModel->statusId());
    }

    public function testLossReasonId(): void
    {
        $this->assertNull($this->leadModel->lossReasonId());

        $this->leadModel->set([
            'loss_reason_id' => 123
        ]);
        $this->assertEquals(123, $this->leadModel->lossReasonId());
    }

    public function testCreatedBy(): void
    {
        $this->assertNull($this->leadModel->createdBy());

        $this->leadModel->set([
            'created_by' => 123
        ]);
        $this->assertEquals(123, $this->leadModel->createdBy());
    }

    public function testUpdatedBy(): void
    {
        $this->assertNull($this->leadModel->updatedBy());

        $this->leadModel->set([
            'updated_by' => 123
        ]);
        $this->assertEquals(123, $this->leadModel->updatedBy());
    }

    public function testCreatedAt(): void
    {
        $this->assertNull($this->leadModel->createdAt());

        $this->leadModel->set([
            'created_at' => 123
        ]);
        $this->assertEquals(123, $this->leadModel->createdAt());
    }

    public function testUpdatedAt(): void
    {
        $this->assertNull($this->leadModel->updatedAt());

        $this->leadModel->set([
            'updated_at' => 123
        ]);
        $this->assertEquals(123, $this->leadModel->updatedAt());
    }

    public function testClosedAt(): void
    {
        $this->assertNull($this->leadModel->closedAt());

        $this->leadModel->set([
            'closed_at' => 123
        ]);
        $this->assertEquals(123, $this->leadModel->closedAt());
    }

    public function testClosedTAskAt(): void
    {
        $this->assertNull($this->leadModel->closedTaskAt());

        $this->leadModel->set([
            'closed_task_at' => 123
        ]);
        $this->assertEquals(123, $this->leadModel->closedTaskAt());
    }

    public function testIsDeleted(): void
    {
        $this->assertNull($this->leadModel->isDeleted());

        $this->leadModel->set([
            'is_deleted' => true
        ]);
        $this->assertTrue($this->leadModel->isDeleted());

        $this->leadModel->set([
            'is_deleted' => false
        ]);
        $this->assertFalse($this->leadModel->isDeleted());
    }

    public function testScore(): void
    {
        $this->assertNull($this->leadModel->score());

        $this->leadModel->set([
            'score' => 123
        ]);
        $this->assertEquals(123, $this->leadModel->score());
    }

    public function testAccountId(): void
    {
        $this->assertNull($this->leadModel->accountId());

        $this->leadModel->set([
            'account_id' => 123
        ]);
        $this->assertEquals(123, $this->leadModel->accountId());
    }
}