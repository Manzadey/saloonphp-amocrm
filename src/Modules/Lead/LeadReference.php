<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Lead;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Note\NoteReferences;
use Manzadey\SaloonAmoCrm\Modules\Tag\TagReference;

readonly class LeadReference
{
    public function __construct(
        protected MainConnector $connector
    )
    {
    }

    public function list(): Requests\LeadListRequest
    {
        return new Requests\LeadListRequest($this->connector);
    }

    public function item(int $id): Requests\LeadItemRequest
    {
        return new Requests\LeadItemRequest($this->connector, $id);
    }

    public function search(string|int $query): Requests\LeadListRequest
    {
        return $this->list()->querySearch($query);
    }

    public function create(LeadModel $model = null): Requests\LeadCreateRequest
    {
        $request = new Requests\LeadCreateRequest($this->connector);

        if ($model instanceof LeadModel) {
            $request->add($model);
        }

        return $request;
    }

    public function update(): Requests\LeadUpdateRequest
    {
        return new Requests\LeadUpdateRequest($this->connector);
    }

    public function customFields(): Requests\LeadCustomFieldsListRequest
    {
        return new Requests\LeadCustomFieldsListRequest($this->connector);
    }

    public function notes(): NoteReferences
    {
        return new NoteReferences($this->connector, 'leads');
    }

    public function tags(): TagReference
    {
        return new TagReference($this->connector, 'leads');
    }
}