<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Lead;

use Manzadey\SaloonAmoCrm\Contracts\CustomFieldsValuesContract;
use Manzadey\SaloonAmoCrm\Contracts\TagsContract;
use Manzadey\SaloonAmoCrm\Contracts\TaskContract;
use Manzadey\SaloonAmoCrm\Modules\Contact\Requests\HasContacts;
use Manzadey\SaloonAmoCrm\Modules\CustomField\Requests\HasCustomFieldsValues;
use Manzadey\SaloonAmoCrm\Modules\Model;
use Manzadey\SaloonAmoCrm\Modules\Tag\Requests\HasTags;

class LeadModel extends Model implements TagsContract, TaskContract, CustomFieldsValuesContract
{
    use HasContacts;
    use HasTags;
    use HasCustomFieldsValues;

    public function id(): ?int
    {
        return $this->get('id');
    }

    public function setId(int $id): static
    {
        return $this->add('id', $id);
    }

    public function link(): ?string
    {
        return $this->get('_links.self.href');
    }

    public function requestId(): ?string
    {
        return $this->get('request_id');
    }

    public function name(): ?string
    {
        return $this->get('name');
    }

    public function setName(string $name): static
    {
        return $this->add('name', $name);
    }

    public function removeName(): static
    {
        return $this->remove('name');
    }

    public function price(): ?int
    {
        return $this->get('price');
    }

    public function setPrice(int $price): static
    {
        return $this->add('price', $price);
    }

    public function removePrice(): static
    {
        return $this->remove('price');
    }

    public function pipelineId(): ?int
    {
        return $this->get('pipeline_id');
    }

    public function setPipelineId(int $pipeline_id): static
    {
        return $this->add('pipeline_id', $pipeline_id);
    }

    public function removePipelineId(): static
    {
        return $this->remove('pipeline_id');
    }

    public function responsibleUserId(): ?int
    {
        return $this->get('responsible_user_id');
    }

    public function setResponsibleUserId(int $pipeline_id): static
    {
        return $this->add('responsible_user_id', $pipeline_id);
    }

    public function removeResponsibleUserId(): static
    {
        return $this->remove('responsible_user_id');
    }

    public function groupId(): ?int
    {
        return $this->get('group_id');
    }

    public function statusId(): ?int
    {
        return $this->get('status_id');
    }

    public function setStatusId(int $statusId): static
    {
        return $this->add('status_id', $statusId);
    }

    public function lossReasonId(): ?int
    {
        return $this->get('loss_reason_id');
    }

    public function createdBy(): ?int
    {
        return $this->get('created_by');
    }

    public function updatedBy(): ?int
    {
        return $this->get('updated_by');
    }

    public function createdAt(): ?int
    {
        return $this->get('created_at');
    }

    public function updatedAt(): ?int
    {
        return $this->get('updated_at');
    }

    public function closedAt(): ?int
    {
        return $this->get('closed_at');
    }

    public function closedTaskAt(): ?int
    {
        return $this->get('closed_task_at');
    }

    public function isDeleted(): ?bool
    {
        return $this->get('is_deleted');
    }

    public function score(): ?int
    {
        return $this->get('score');
    }

    public function accountId(): ?int
    {
        return $this->get('account_id');
    }
}