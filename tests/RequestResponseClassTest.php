<?php

declare(strict_types=1);

namespace Manzadey\tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use Saloon\Http\Request;
use Saloon\Http\Response;
use SplFileInfo;

/**
 * Запрос, который сузил возвращаемый тип до своего Response, обязан объявить
 * $response — иначе Saloon отдаст базовый Response и вызов упрётся в TypeError.
 * Так был найден TaskItemRequest без $response.
 */
class RequestResponseClassTest extends TestCase
{
    #[DataProvider('narrowedRequests')]
    public function testResponsePropertyMatchesNarrowedReturnType(
        string $requestClass,
        string $method,
        string $declaredResponse,
    ): void {
        $this->assertSame(
            $declaredResponse,
            (new ReflectionClass($requestClass))->getDefaultProperties()['response'] ?? null,
            "$requestClass::$method() объявляет $declaredResponse, но \$response не задан или не совпадает",
        );
    }

    /**
     * @return iterable<string, array{class-string<Request>, string, class-string<Response>}>
     */
    public static function narrowedRequests(): iterable
    {
        foreach (self::requestClasses() as $requestClass) {
            foreach ((new ReflectionClass($requestClass))->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->getDeclaringClass()->getName() !== $requestClass) {
                    continue;
                }

                $returnType = $method->getReturnType();

                if (! $returnType instanceof ReflectionNamedType || $returnType->isBuiltin()) {
                    continue;
                }

                $returned = $returnType->getName();

                if ($returned === Response::class || ! is_subclass_of($returned, Response::class)) {
                    continue;
                }

                yield "$requestClass::{$method->getName()}" => [$requestClass, $method->getName(), $returned];
            }
        }
    }

    /**
     * @return iterable<class-string<Request>>
     */
    private static function requestClasses(): iterable
    {
        $src = dirname(__DIR__) . '/src';

        /** @var iterable<SplFileInfo> $files */
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($src));

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $class = 'Manzadey\SaloonAmoCrm\\' . str_replace(
                '/',
                '\\',
                substr($file->getPathname(), strlen($src) + 1, -4),
            );

            if (class_exists($class) && is_subclass_of($class, Request::class)) {
                yield $class;
            }
        }
    }
}
