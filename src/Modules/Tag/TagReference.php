<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Tag;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Contracts\TagsContract;
use Manzadey\SaloonAmoCrm\Modules\Tag\Requests\TagAttachRequest;
use Manzadey\SaloonAmoCrm\Modules\Tag\Requests\TagCreateRequest;
use Manzadey\SaloonAmoCrm\Modules\Tag\Requests\TagListRequest;

class TagReference
{
    public function __construct(
        protected readonly MainConnector $connector,
        protected readonly string $entityType,
    ) {
    }

    public function list(): TagListRequest
    {
        return new TagListRequest($this->connector, $this->entityType);
    }

    /**
     * @param TagModel|array<string, mixed> $tag
     */
    public function create(TagModel|array $tag): TagCreateRequest
    {
        return TagCreateRequest::make($this->connector, $this->entityType)->add($tag);
    }

    public function update(TagsContract $model): TagAttachRequest
    {
        return TagAttachRequest::make($this->connector, $this->entityType)->add($model);
    }
}
