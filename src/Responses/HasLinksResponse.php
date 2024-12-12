<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Responses;

use Saloon\Http\Response;

/**
 * @mixin Response
 */
trait HasLinksResponse
{
    public function links(): ?array
    {
        return $this->json('_links');
    }

    public function selfPageUrl(): ?string
    {
        return $this->json('_links.self.href');
    }

    public function nextPageUrl(): ?string
    {
        return $this->json('_links.next.href');
    }

    public function firstPageUrl(): ?string
    {
        return $this->json('_links.first.href');
    }

    public function prefPageUrl(): ?string
    {
        return $this->json('_links.prev.href');
    }
}