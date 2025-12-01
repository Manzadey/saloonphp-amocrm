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

    /**
     * @return int|null ID сделки
     */
    public function id(): ?int
    {
        return $this->get('id');
    }

    public function setId(int $id): static
    {
        return $this->add('id', $id);
    }

    /**
     * @return string|null Cсылка на сделку
     */
    public function link(): ?string
    {
        return $this->get('_links.self.href');
    }

    /**
     * @return int|array|null Строка переданная при запросе или порядковый указатель, если параметр не передан
     */
    public function requestId(): int|array|null
    {
        return $this->get('request_id');
    }

    public function setRequestId(string $requestId): static
    {
        return $this->add('request_id', $requestId);
    }

    /**
     * @return string|null Название сделки. Поле не является обязательным
     */
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

    /**
     * @return int|null Бюджет сделки. Поле не является обязательным
     */
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

    /**
     * @return int|null ID воронки, в которую добавляется сделка. Поле не является обязательным
     */
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

    /**
     * @return int|null ID пользователя, ответственного за сделку. Поле не является обязательным
     */
    public function responsibleUserId(): ?int
    {
        return $this->get('responsible_user_id');
    }

    public function setResponsibleUserId(int $responsibleUserId): static
    {
        return $this->add('responsible_user_id', $responsibleUserId);
    }

    public function removeResponsibleUserId(): static
    {
        return $this->remove('responsible_user_id');
    }

    /**
     * @return int|null ID группы, в которой состоит ответственны пользователь за сделку
     */
    public function groupId(): ?int
    {
        return $this->get('group_id');
    }

    /**
     * @return int|null ID статуса, в который добавляется сделка, по-умолчанию – первый этап главной воронки
     */
    public function statusId(): ?int
    {
        return $this->get('status_id');
    }

    public function setStatusId(int $statusId): static
    {
        return $this->add('status_id', $statusId);
    }

    /**
     * @return int|null ID причины отказа
     */
    public function lossReasonId(): ?int
    {
        return $this->get('loss_reason_id');
    }

    /**
     * @return int|null ID пользователя, создающий сделку
     */
    public function createdBy(): ?int
    {
        return $this->get('created_by');
    }

    /**
     * @return int|null ID пользователя, изменяющий сделку
     */
    public function updatedBy(): ?int
    {
        return $this->get('updated_by');
    }

    /**
     * @return int|null Дата создания сделки, передается в Unix Timestamp

     */
    public function createdAt(): ?int
    {
        return $this->get('created_at');
    }

    /**
     * @return int|null Дата изменения сделки, передается в Unix Timestamp
     */
    public function updatedAt(): ?int
    {
        return $this->get('updated_at');
    }

    /**
     * @return int|null Дата закрытия сделки, передается в Unix Timestamp
     */
    public function closedAt(): ?int
    {
        return $this->get('closed_at');
    }

    /**
     * @return int|null Дата ближайшей задачи к выполнению, передается в Unix Timestamp
     */
    public function closedTaskAt(): ?int
    {
        return $this->get('closed_task_at');
    }

    /**
     * @return bool|null Удалена ли сделка
     */
    public function isDeleted(): ?bool
    {
        return $this->get('is_deleted');
    }

    /**
     * @return int|null Скоринг сделки
     */
    public function score(): ?int
    {
        return $this->get('score');
    }

    /**
     * @return int|null ID аккаунта, в котором находится сделка
     */
    public function accountId(): ?int
    {
        return $this->get('account_id');
    }
}