<?php

declare(strict_types=1);

namespace Manzadey\tests;

use DateTimeImmutable;
use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Contact\ContactReference;
use Manzadey\SaloonAmoCrm\Modules\Lead\LeadReference;
use Manzadey\SaloonAmoCrm\Modules\Task\TaskReference;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use Saloon\Http\Auth\AccessTokenAuthenticator;
use Saloon\Http\Request;
use SplFileInfo;

/**
 * Обязательные параметры запроса приходят только через конструктор и после этого
 * неизменяемы — сеттеров на них нет. Опциональный query остаётся fluent: изоляцию
 * между отправками обеспечивает Saloon через PendingRequest.
 */
class ImmutableRequestParamsTest extends TestCase
{
    /**
     * @return iterable<string, array{class-string, string}>
     */
    public static function constructorProperties(): iterable
    {
        foreach (self::requestClasses() as $class) {
            $constructor = (new ReflectionClass($class))->getConstructor();

            if ($constructor === null || $constructor->getDeclaringClass()->getName() !== $class) {
                continue;
            }

            foreach ($constructor->getParameters() as $parameter) {
                if (! $parameter->isPromoted()) {
                    continue;
                }

                yield "$class::\$" . $parameter->getName() => [$class, $parameter->getName()];
            }
        }
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('constructorProperties')]
    public function testPromotedRequestPropertiesAreReadonly(string $class, string $property): void
    {
        self::assertTrue(
            (new ReflectionClass($class))->getProperty($property)->isReadonly(),
            sprintf('%s::$%s задаётся конструктором и должен быть readonly', $class, $property)
        );
    }

    /**
     * @return array<string, array{class-string, string}>
     */
    public static function createFactories(): array
    {
        return [
            'leads'    => [LeadReference::class, 'create'],
            'contacts' => [ContactReference::class, 'create'],
            'tasks'    => [TaskReference::class, 'create'],
        ];
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('createFactories')]
    public function testCreateRequiresTheModel(string $class, string $method): void
    {
        $parameter = (new ReflectionMethod($class, $method))->getParameters()[0];

        self::assertFalse(
            $parameter->isOptional(),
            sprintf('%s::%s() без модели создаёт запрос с пустым телом — модель обязательна', $class, $method)
        );

        $type = $parameter->getType();

        self::assertInstanceOf(ReflectionNamedType::class, $type);
        self::assertFalse($type->allowsNull(), sprintf('%s::%s(): модель не может быть null', $class, $method));
    }

    public function testConnectorStaysReadableAfterConstruction(): void
    {
        $reference = new LeadReference(new MainConnector(
            'test.amocrm.ru',
            static fn () => new AccessTokenAuthenticator('token', 'refresh', new DateTimeImmutable()),
        ));

        self::assertSame(1, $reference->item(1)->query()->get('limit'));
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
