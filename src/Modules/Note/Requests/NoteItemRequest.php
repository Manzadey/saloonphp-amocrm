<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Note\Requests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Note\Responses\NoteItemResponse;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class NoteItemRequest extends Request
{
    protected Method $method = Method::GET;

    protected ?string $response = NoteItemResponse::class;

    public function __construct(
        protected readonly MainConnector $connector,
        protected readonly string $entityType,
        protected readonly int $id,
        protected readonly ?int $entityId = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function resolveEndpoint(): string
    {
        $path[] = $this->entityType;

        if ($this->entityId !== null) {
            $path[] = $this->entityId;
        }

        $path[] = 'notes';
        $path[] = $this->id;

        return implode('/', $path);
    }

    public function send(): Response|NoteItemResponse
    {
        return $this->connector->send($this);
    }
}