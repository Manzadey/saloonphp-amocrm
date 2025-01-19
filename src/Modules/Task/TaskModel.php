<?php

namespace Manzadey\SaloonAmoCrm\Modules\Task;

use Manzadey\SaloonAmoCrm\Contracts\HasTaskContract;
use Manzadey\SaloonAmoCrm\Modules\Lead\LeadModel;
use Manzadey\SaloonAmoCrm\Modules\Model;

class TaskModel extends Model
{
    public function id(): ?int
    {
        return $this->get('id');
    }

    public function setId(int $id): static
    {
        return $this->add('id', $id);
    }


    public function createdBy(): ?int
    {
        return $this->get('created_by');
    }

    public function setCreatedBy(int $createdBy): static
    {
        return $this->add('created_by', $createdBy);
    }

    public function updatedBy(): ?int
    {
        return $this->get('updated_by');
    }

    public function setUpdatedBy(int $updatedBy): static
    {
        return $this->add('updated_by', $updatedBy);
    }

    public function createdAt(): ?int
    {
        return $this->get('created_at');
    }

    public function setCreatedAt(int $createdAt): static
    {
        return $this->add('created_at', $createdAt);
    }

    public function updatedAt(): ?int
    {
        return $this->get('updated_at');
    }

    public function setUpdatedAt(int $updatedAt): static
    {
        return $this->add('updated_at', $updatedAt);
    }

    public function responsibleUserId(): ?int
    {
        return $this->get('responsible_user_id');
    }

    public function setResponsibleUserId(int $responsibleUserId): static
    {
        return $this->add('responsible_user_id', $responsibleUserId);
    }


    public function groupId(): ?int
    {
        return $this->get('group_id');
    }

    public function setGroupId(int $groupId): static
    {
        return $this->add('group_id', $groupId);
    }

    public function setEntity(HasTaskContract $model): static
    {
        $type = match (get_class($model)) {
            LeadModel::class => 'leads',
        };

        return $this->setEntityType($type)->setEntityId($model->id());
    }

    public function entityId(): ?int
    {
        return $this->get('entity_id');
    }

    public function setEntityId(int $entityId): static
    {
        return $this->add('entity_id', $entityId);
    }

    public function entityType(): ?string
    {
        return $this->get('entity_type');
    }

    public function setEntityType(string $entityType): static
    {
        return $this->add('entity_type', $entityType);
    }

    public function isCompleted(): ?bool
    {
        return $this->get('is_completed');
    }

    public function setIsCompleted(bool $isCompleted): static
    {
        return $this->add('is_completed', $isCompleted);
    }

    public function taskTypeId(): ?int
    {
        return $this->get('task_type_id');
    }

    public function setTaskTypeId(int $taskTypeId): static
    {
        return $this->add('task_type_id', $taskTypeId);
    }

    public function text(): ?string
    {
        return $this->get('text');
    }

    public function setText(string $text): static
    {
        return $this->add('text', $text);
    }

    public function duration(): ?int
    {
        return $this->get('duration');
    }

    public function setDuration(int $duration): static
    {
        return $this->add('duration', $duration);
    }

    public function completeTill(): ?int
    {
        return $this->get('complete_till');
    }

    public function setCompleteTill(int $completeTill): static
    {
        return $this->add('complete_till', $completeTill);
    }

    public function result(): ?array
    {
        return $this->get('result');
    }

    public function setResult(array $result): static
    {
        return $this->add('result', $result);
    }

    public function accountId(): ?int
    {
        return $this->get('account_id');
    }

    public function setAccountId(int $accountId): static
    {
        return $this->add('account_id', $accountId);
    }
}