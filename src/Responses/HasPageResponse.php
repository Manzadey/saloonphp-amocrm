<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Responses;

use Saloon\Http\Response;

/**
 * @mixin Response
 */
trait HasPageResponse
{
    public function page(): ?int
    {
        return $this->json('_page');
    }
}