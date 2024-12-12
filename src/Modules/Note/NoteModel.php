<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Note;

use Manzadey\SaloonAmoCrm\Modules\Model;

class NoteModel extends Model
{
    public function getId(): ?int
    {
        return $this->get('id');
    }

    public function entityId(): ?int
    {
        return $this->get('entity_id');
    }

    public function setEntityId(?int $id = null): static
    {
        return $this->add('entity_id', $id);
    }

    public function createdBy(): ?int
    {
        return $this->get('created_by');
    }

    public function setCreatedBy(int $id): static
    {
        return $this->add('created_by', $id);
    }

    public function noteType(): ?string
    {
        return $this->get('note_type');
    }

    public function noteTypeEnum(): ?NoteTypeEnum
    {
        $type = $this->noteType();

        if ($type === null) {
            return null;
        }

        return NoteTypeEnum::from($type);
    }

    public function setNoteType(NoteTypeEnum $type): static
    {
        return $this->add('note_type', $type->value);
    }

    public function responsibleUserId(): ?int
    {
        return $this->get('responsible_user_id');
    }

    public function setResponsibleUserId(int $id): static
    {
        return $this->add('responsible_user_id', $id);
    }

    public function params(): ?array
    {
        return $this->get('params');
    }

    public function setParams(array $params): static
    {
        return $this->add('params', $params);
    }

    public function requestId(): ?string
    {
        return $this->get('request_id');
    }

    public function setRequestId(string $id): static
    {
        return $this->add('request_id', $id);
    }

    public function link(): ?string
    {
        return $this->get('_links.self.href');
    }

    public function isNeedToTriggerDigitalPipeline(): ?bool
    {
        return $this->get('is_need_to_trigger_digital_pipeline');
    }

    public function setIsNeedToTriggerDigitalPipeline(bool $flag): static
    {
        return $this->add('is_need_to_trigger_digital_pipeline', $flag);
    }

    public function commonNote(string $text): static
    {
        return $this
            ->setNoteType(NoteTypeEnum::Common)
            ->setParams(compact('text'));
    }

    public function callIn(CallInNoteModel $model): static
    {
        return $this
            ->setNoteType(NoteTypeEnum::CallIn)
            ->setParams($model->all());
    }
}