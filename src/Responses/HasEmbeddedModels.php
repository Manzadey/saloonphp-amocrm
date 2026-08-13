<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Responses;

use Manzadey\SaloonAmoCrm\Collections\ModelCollection;
use Manzadey\SaloonAmoCrm\Modules\Model;
use Saloon\Http\Response;

/**
 * @mixin Response
 */
trait HasEmbeddedModels
{
    /**
     * @template T of Model
     *
     * @param  class-string<T> $model
     * @return ModelCollection<T>
     */
    protected function embedded(string $key, string $model): ModelCollection
    {
        return ModelCollection::of($model, $this->json("_embedded.$key"));
    }

    /**
     * Пустое тело — это отсутствие сущности, а не сущность без полей: item-запрос по
     * несуществующему id отдаёт 204, и модель из него строить нечего.
     *
     * @template T of Model
     *
     * @param  class-string<T> $model
     * @return T|null
     */
    protected function single(string $model): ?Model
    {
        $data = $this->json();

        return $data === [] ? null : new $model($data);
    }
}
