<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Tag\Requests;

use Manzadey\SaloonAmoCrm\Modules\Tag\TagModel;

trait HasTags
{
    /**
     * @return list<TagModel>
     */
    public function tags(): array
    {
        return array_map(
            static fn (array $tag): TagModel => new TagModel($tag),
            $this->get('_embedded.tags', []),
        );
    }

    /**
     * @param list<TagModel|array<string, mixed>>|null $tags
     */
    public function setTags(?array $tags = null): static
    {
        $embedded = $this->get('_embedded', []);

        if (empty($tags)) {
            unset($embedded['tags']);

            return $this->add('_embedded', $embedded);
        }

        $embedded['tags'] = array_map(
            static fn (TagModel|array $tag): array => $tag instanceof TagModel ?
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

    /**
     * @param TagModel|array<string, mixed> $tag
     */
    public function addTag(TagModel|array $tag): static
    {
        $tags = $this->tags();
        $tags[] = $tag;

        return $this->setTags($tags);
    }

    /**
     * @return list<TagModel>
     */
    public function tagsToAdd(): array
    {
        return array_map(
            static fn (array $tag): TagModel => new TagModel($tag),
            $this->get('tags_to_add', []),
        );
    }

    /**
     * @param TagModel|array<string, mixed> $tag
     */
    public function appendToTagsToAdd(TagModel|array $tag): static
    {
        $tagsToAdd = $this->tagsToAdd();
        $tagsToAdd[] = $tag;

        foreach ($tagsToAdd as $i => $tagToAdd) {
            if ($tagToAdd instanceof TagModel) {
                $tagsToAdd[$i] = $tagToAdd->all();
            }
        }

        return $this->add('tags_to_add', $tagsToAdd);
    }
}
