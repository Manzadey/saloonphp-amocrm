<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Note\Requests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Note\NoteTypeEnum;
use Manzadey\SaloonAmoCrm\Modules\Note\Responses\NoteListResponse;
use Manzadey\SaloonAmoCrm\Query;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class NoteListRequest extends Request
{
    use Query\HasPageQuery;
    use Query\HasLimitQuery;
    use Query\HasFilterQuery;

    protected Method $method = Method::GET;

    protected ?string $response = NoteListResponse::class;

    public function __construct(
        protected readonly MainConnector $connector,
        protected readonly string $entityType,
        protected readonly ?int $entityId = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function resolveEndpoint(): string
    {
        $path[] = $this->entityType;
        if ($this->entityId) {
            $path[] = $this->entityId;
        }
        $path[] = 'notes';

        return implode('/', $path);
    }

    public function send(): Response|NoteListResponse
    {
        return $this->connector->send($this);
    }

    /**
     * @param array<int>|int $id
     * @return $this
     */
    public function filterId(array|int $id): static
    {
        return $this->filter('id', $id);
    }

    /**
     * @param array<int> $ids
     * @return $this
     */
    public function filterEntityId(array $ids): static
    {
        return $this->filter('entity_id', $ids);
    }

    public function filterNoteType(NoteTypeEnum $noteTypeEnum): static
    {
        return $this->filter('note_type', $noteTypeEnum->value);
    }
}