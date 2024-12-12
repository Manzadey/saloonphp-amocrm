<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Tag;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Contracts\HasTagsContract;
use Manzadey\SaloonAmoCrm\Modules\Lead\LeadModel;
use Manzadey\SaloonAmoCrm\Modules\Tag\Requests\TagAttachRequest;
use Manzadey\SaloonAmoCrm\Modules\Tag\Requests\TagCreateRequest;
use Manzadey\SaloonAmoCrm\Modules\Tag\Requests\TagListRequest;

readonly class TagReference
{
    public function __construct(
        protected MainConnector $connector,
        protected string $entityType,
    ) {
    }

    public function list(): TagListRequest
    {
        return new TagListRequest($this->connector, $this->entityType);
    }

    public function create(TagModel|array $tag = null): TagCreateRequest
    {
        return TagCreateRequest::make($this->connector, $this->entityType)->when(
            $tag !== null,
            static fn(TagCreateRequest $request): TagCreateRequest => $request->tag($tag)
        );
    }

    public function update(?HasTagsContract $model = null): TagAttachRequest
    {
        return TagAttachRequest::make($this->connector, $this->entityType)
            ->when($model !== null, static fn(TagAttachRequest $request): TagAttachRequest => $request->model($model));
    }

    public function updateLead(LeadModel $model): TagAttachRequest
    {
        return $this->update($model);
    }
}