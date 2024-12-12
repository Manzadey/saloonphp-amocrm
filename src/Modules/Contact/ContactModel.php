<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Contact;

use Manzadey\SaloonAmoCrm\Modules\CustomField\Requests\HasCustomFieldsValues;
use Manzadey\SaloonAmoCrm\Modules\Model;

/**
 * @link https://www.amocrm.ru/developers/content/crm_platform/contacts-api#contact-detail
 */
class ContactModel extends Model
{
    use HasCustomFieldsValues;

    public function id(): ?int
    {
        return $this->get('id');
    }

    public function setId(int $id): static
    {
        return $this->add('id', $id);
    }

    public function name(): string
    {
        return $this->get('name');
    }

    public function setName(string $name): static
    {
        return $this->add('name', $name);
    }

    public function firstName(): string
    {
        return $this->get('first_name');
    }

    public function setFirstName(string $firstName): static
    {
        return $this->add('first_name', $firstName);
    }

    public function lastName(): string
    {
        return $this->get('last_name');
    }

    public function setLastName(string $lastName): static
    {
        return $this->add('last_name', $lastName);
    }

    public function responsibleUserId(): int
    {
        return $this->get('responsible_user_id');
    }

    public function setResponsibleUserId(int $id): static
    {
        return $this->add('responsible_user_id', $id);
    }

    public function groupId(): ?int
    {
        return $this->get('group_id');
    }

    public function setGroupId(int $id): static
    {
        return $this->add('group_id',$id);
    }

    public function createdBy(): ?int
    {
        return $this->get('created_by');
    }

    public function setCreatedBy(int $id): static
    {
        return $this->add('created_by', $id);
    }

    public function updatedBy(): ?int
    {
        return $this->get('updated_by');
    }

    public function setUpdatedBy(int $id): static
    {
        return $this->add('updated_by', $id);
    }

    public function createdAt(): ?int
    {
        return $this->get('created_at');
    }

    public function updatedAt(): ?int
    {
        return $this->get('updated_at');
    }

    public function setUpdatedAt(int $timestamp): static
    {
        return $this->add('updated_at', $timestamp);
    }

    public function isDeleted(): ?bool
    {
        return $this->get('is_deleted');
    }

    public function setIsDeleted(bool $isDeleted): static
    {
        return $this->add('is_deleted', $isDeleted);
    }

    public function closestTaskAt(): ?int
    {
        return $this->get('closest_task_at');
    }

    public function setClosestTaskAt(int $timestamp): static
    {
        return $this->add('closest_task_at', $timestamp);
    }

    public function accountId(): ?int
    {
        return $this->get('account_id');
    }

    public function setAccountId(int $id): static
    {
        return $this->add('account_id', $id);
    }

    public function setCreatedAt(int $timestamp): static
    {
        return $this->add('created_at', $timestamp);
    }

    public function isUnsorted(): ?bool
    {
        return $this->get('is_unsorted');
    }

    public function isMain(): ?bool
    {
        return $this->get('is_main');
    }

    public function setIsMain(bool $isMain): static
    {
        return $this->add('is_main', $isMain);
    }

    public function link(): ?string
    {
        return $this->get('_links.self.href');
    }
}