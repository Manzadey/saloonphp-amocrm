<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Note\Requests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Note\NoteFilter;
use Manzadey\SaloonAmoCrm\Modules\Note\Responses\NoteListResponse;
use Manzadey\SaloonAmoCrm\Query;
use Manzadey\SaloonAmoCrm\Requests\SendsTypedResponse;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class NoteListRequest extends Request
{
    use SendsTypedResponse;
    use Traits\HasNoteWithQuery;
    use Query\HasPageQuery;
    use Query\HasLimitQuery;
    use Traits\HasNoteOrderQuery;
    /** @use Query\HasFilterQuery<NoteFilter> */
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

    public function send(): NoteListResponse
    {
        return $this->sendTyped(NoteListResponse::class);
    }
}
