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

    /**
     * @return list<TagModel>
     */
    public function tags(): array;

    /**
     * @param list<TagModel|array<string, mixed>> $tags
     */
    public function setTags(array $tags): static;

    /**
     * @param TagModel|array<string, mixed> $tag
     */
    public function addTag(TagModel|array $tag): static;
}
