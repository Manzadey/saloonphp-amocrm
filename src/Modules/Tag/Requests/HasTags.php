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
        $embedded = $this->get('_embedded', []);

        if (empty($tags)) {
            unset($embedded['tags']);

            return $this->add('_embedded', $embedded);
        }

        $embedded['tags'] = array_map(
            static fn(TagModel|array $tag): array => $tag instanceof TagModel ?
                $tag->all() :
                $tag,
            $tags
        );

        return $this->add('_embedded', $embedded);
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

    public function tagsToAdd(): array
    {
        return array_map(
            static fn(array $tag): TagModel => new TagModel($tag),
            $this->get('tags_to_add', []),
        );
    }

    public function appendToTagsToAdd(TagModel|array $tag): static
    {
        $tagsToAdd = $this->tagsToAdd();
        $tagsToAdd[] = $tag;

        foreach ($tagsToAdd as $i => $tagToAdd) {
            if ($tagToAdd instanceof TagModel) {
                $tagsToAdd[$i] = $tag->all();
            }
        }

        return $this->add('tags_to_add', $tagsToAdd);
    }
}