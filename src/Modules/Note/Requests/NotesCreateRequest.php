<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Note\Requests;

use InvalidArgumentException;
use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Note\CommonNoteModel;
use Manzadey\SaloonAmoCrm\Modules\Note\NoteModel;
use Manzadey\SaloonAmoCrm\Modules\Note\Responses\NoteCreateResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class NotesCreateRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    protected ?string $response = NoteCreateResponse::class;

    public function __construct(
        protected readonly MainConnector $connector,
        protected readonly string $entityType,
        protected readonly ?int $entityId = null,
    ) {
    }

    public function resolveEndpoint(): string
    {
        $path[] = $this->entityType;

        if ($this->entityId) {
            $path[] = $this->entityId;
        }

        $path[] = 'notes';

        return implode('/', $path);
    }

    public function add(NoteModel|array $model): static
    {
        if (is_array($model)) {
            $model = new NoteModel($model);
        }

        $this->body()->add(value: $model->all());

        return $this;
    }

    public function addCommonNote(string|CommonNoteModel $text, ?int $entityId = null): static
    {
        if ($text instanceof CommonNoteModel) {
            $modelText = $text->getText();

            if ($modelText === null) {
                throw new InvalidArgumentException('Text is required');
            }

            $text = $modelText;
        }

        return $this->add(
            NoteModel::make()->setEntityId($entityId)->commonNote($text)
        );
    }

    public function send(): Response|NoteCreateResponse
    {
        return $this->connector->send($this);
    }
}