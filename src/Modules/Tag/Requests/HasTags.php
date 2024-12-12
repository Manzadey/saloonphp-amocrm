<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Tag\Requests;

use Manzadey\SaloonAmoCrm\Modules\Tag\TagModel;

trait HasTags
{
    public function tags(): array
    {
        return array_map(
            static fn(array $tag): TagModel => new TagModel($tag),
            $this->get('_embedded.tags', []),
        );
    }

    public function setTags(?array $tags = null): static
    {
        return $this->add(
            key: '_embedded',
            value: array_merge_recursive($this->get('_embedded', []), [
                    'tags' => is_array($tags) ? array_map(
                        static fn(TagModel|array $tag): array => $tag instanceof TagModel ?
                            $tag->all() :
                            $tag,
                        $tags
                    ) : $tags
                ]
            )
        );
    }

    public function clearTags(): static
    {
        return $this->setTags();
    }

    public function addTag(TagModel|array $tag): static
    {
        $tags = $this->tags();
        $tags[] = $tag;

        return $this->setTags($tags);
    }
}