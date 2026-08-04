<?php

declare(strict_types=1);

namespace Manzadey\tests;

use InvalidArgumentException;
use Manzadey\SaloonAmoCrm\Contracts\TaskContract;
use Manzadey\SaloonAmoCrm\Modules\Lead\LeadModel;
use Manzadey\SaloonAmoCrm\Modules\Task\TaskModel;
use PHPUnit\Framework\TestCase;

class TaskModelTest extends TestCase
{
    public function testSetEntitySupportsLead(): void
    {
        $lead = (new LeadModel())->setId(42);

        $task = (new TaskModel())->setEntity($lead);

        self::assertSame('leads', $task->entityType());
        self::assertSame(42, $task->entityId());
    }

    public function testSetEntityThrowsForUnsupportedModel(): void
    {
        $model = new class implements TaskContract {
            public function id(): ?int
            {
                return 7;
            }
        };

        $this->expectException(InvalidArgumentException::class);

        (new TaskModel())->setEntity($model);
    }
}
