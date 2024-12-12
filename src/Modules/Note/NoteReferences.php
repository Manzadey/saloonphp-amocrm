<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Note;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Note\Requests\NoteItemRequest;
use Manzadey\SaloonAmoCrm\Modules\Note\Requests\NoteListRequest;
use Manzadey\SaloonAmoCrm\Modules\Note\Requests\NotesCreateRequest;

readonly class NoteReferences
{
    public function __construct(
        protected MainConnector $connector,
        protected string $entityType,
    )
    {
    }

    public function list(): NoteListRequest
    {
        return new NoteListRequest($this->connector, $this->entityType);
    }

    public function item(int $id, ?int $entityId = null): NoteItemRequest
    {
        return new NoteItemRequest($this->connector, $this->entityType, $id, $entityId);
    }

    public function create(?int $entityId = null): NotesCreateRequest
    {
        return new NotesCreateRequest($this->connector, $this->entityType, $entityId);
    }
}