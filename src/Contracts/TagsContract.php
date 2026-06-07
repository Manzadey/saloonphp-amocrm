<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Contracts;

use Manzadey\SaloonAmoCrm\Modules\Tag\TagModel;

interface TagsContract
{
    /**
     * @return array<string, mixed>
     */
    public function all(): array;

    public function tags(): array;

    public function setTags(array $tags): static;

    public function addTag(TagModel|array $tag): static;
}
