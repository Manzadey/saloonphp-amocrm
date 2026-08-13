<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Tag\Requests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Contracts\TagsContract;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class TagAttachRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    public function __construct(
        protected readonly MainConnector $connector,
        protected readonly string $entityType,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function resolveEndpoint(): string
    {
        return "/$this->entityType";
    }

    public function add(TagsContract $model): static
    {
        $this->body()->add(value: $model->all());

        return $this;
    }

    /**
     * Типизированного ответа здесь нет намеренно: PATCH `/{entity}` отдаёт
     * `_embedded.{сущность}` — форма зависит от типа сущности, а тип известен
     * запросу, не ответу. Появится в Фазе 4a вместе с update-запросами сущностей.
     */
    public function send(): Response
    {
        return $this->connector->send($this);
    }
}
