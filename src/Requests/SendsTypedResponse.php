<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Requests;

use Saloon\Http\Response;
use UnexpectedValueException;

/**
 * Saloon строит ответ из $response запроса, но типизирует Connector::send()
 * базовым Response — конкретный класс теряется. Проверка ниже возвращает его
 * обратно по-настоящему, а не докблоком.
 */
trait SendsTypedResponse
{
    /**
     * @template T of Response
     *
     * @param  class-string<T> $responseClass Тот же класс, что в $this->response
     * @return T
     */
    protected function sendTyped(string $responseClass): Response
    {
        $response = $this->connector->send($this);

        if (! $response instanceof $responseClass) {
            throw new UnexpectedValueException(
                sprintf('%s: ожидался %s, получен %s', static::class, $responseClass, $response::class)
            );
        }

        return $response;
    }
}
