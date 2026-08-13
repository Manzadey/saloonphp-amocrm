<?php

declare(strict_types=1);

namespace Manzadey\tests;

use DateTimeImmutable;
use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Contact\ContactFilter;
use Manzadey\SaloonAmoCrm\Modules\Contact\Requests\ContactListRequest;
use Manzadey\SaloonAmoCrm\Modules\CustomField\CustomFieldFilter;
use Manzadey\SaloonAmoCrm\Modules\CustomField\Requests\CustomFieldListRequest;
use Manzadey\SaloonAmoCrm\Modules\Lead\LeadFilter;
use Manzadey\SaloonAmoCrm\Modules\Lead\Requests\LeadListRequest;
use Manzadey\SaloonAmoCrm\Modules\Note\NoteFilter;
use Manzadey\SaloonAmoCrm\Modules\Note\Requests\NoteListRequest;
use Manzadey\SaloonAmoCrm\Modules\Tag\Requests\TagListRequest;
use Manzadey\SaloonAmoCrm\Modules\Tag\TagFilter;
use Manzadey\SaloonAmoCrm\Modules\Task\Requests\TaskListRequest;
use Manzadey\SaloonAmoCrm\Modules\Task\TaskFilter;
use Manzadey\SaloonAmoCrm\Query\HasFilterQuery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Saloon\Http\Auth\AccessTokenAuthenticator;
use Saloon\Http\Request;
use Saloon\Repositories\ArrayStore;

class HasFilterQueryTest extends TestCase
{
    private MainConnector $connector;

    protected function setUp(): void
    {
        $this->connector = new MainConnector(
            'test.amocrm.ru',
            static fn () => new AccessTokenAuthenticator('token', 'refresh', new DateTimeImmutable()),
        );
    }

    /**
     * Каждый list-запрос принимает фильтр-объект своей сущности, а не строковые ключи.
     *
     * @return array<string, array{callable(MainConnector): Request, callable(): ArrayStore, array<string, mixed>}>
     */
    public static function requestsWithFilters(): array
    {
        return [
            'leads' => [
                static fn (MainConnector $c): Request => new LeadListRequest($c),
                static fn (): ArrayStore => LeadFilter::make()->pipelineId(7)->price(0, 1000),
                ['pipeline_id' => 7, 'price' => ['from' => 0, 'to' => 1000]],
            ],
            'contacts' => [
                static fn (MainConnector $c): Request => new ContactListRequest($c),
                static fn (): ArrayStore => ContactFilter::make()->name('Иванов')->createdBy(3),
                ['name' => 'Иванов', 'created_by' => 3],
            ],
            'tasks' => [
                static fn (MainConnector $c): Request => new TaskListRequest($c),
                static fn (): ArrayStore => TaskFilter::make()->isCompleted()->entityId([1, 2]),
                ['is_completed' => true, 'entity_id' => [1, 2]],
            ],
            'notes' => [
                static fn (MainConnector $c): Request => new NoteListRequest($c, 'leads'),
                static fn (): ArrayStore => NoteFilter::make()->id(5)->entityId([1, 2]),
                ['id' => 5, 'entity_id' => [1, 2]],
            ],
            'tags' => [
                static fn (MainConnector $c): Request => new TagListRequest($c, 'leads'),
                static fn (): ArrayStore => TagFilter::make()->name('VIP'),
                ['name' => 'VIP'],
            ],
            'custom fields' => [
                static fn (MainConnector $c): Request => new CustomFieldListRequest($c, 'leads'),
                static fn (): ArrayStore => CustomFieldFilter::make()->type('text'),
                ['type' => ['text']],
            ],
        ];
    }

    /**
     * @param callable(MainConnector): Request $requestFactory
     * @param callable(): ArrayStore           $filterFactory
     * @param array<string, mixed>             $expected
     */
    #[DataProvider('requestsWithFilters')]
    public function testAddFilterTransfersEveryKey(
        callable $requestFactory,
        callable $filterFactory,
        array $expected,
    ): void {
        $request = $requestFactory($this->connector)->addFilter($filterFactory());

        self::assertSame($expected, $request->query()->get('filter'));
    }

    public function testRepeatedKeyOverwritesInsteadOfAccumulating(): void
    {
        $request = (new TaskListRequest($this->connector))
            ->addFilter(TaskFilter::make()->entityType('leads'))
            ->addFilter(TaskFilter::make()->entityType('contacts'));

        self::assertSame(['entity_type' => 'contacts'], $request->query()->get('filter'));
    }

    public function testDistinctKeysFromSeparateFiltersCoexist(): void
    {
        $request = (new LeadListRequest($this->connector))
            ->addFilter(LeadFilter::make()->id(1))
            ->addFilter(LeadFilter::make()->name('Сделка'));

        self::assertSame(['id' => 1, 'name' => 'Сделка'], $request->query()->get('filter'));
    }

    /**
     * @param callable(MainConnector): Request $requestFactory
     * @param callable(): ArrayStore           $filterFactory
     * @param array<string, mixed>             $expected
     */
    #[DataProvider('requestsWithFilters')]
    public function testStringFilterIsNotPartOfThePublicApi(
        callable $requestFactory,
        callable $filterFactory,
        array $expected,
    ): void {
        $method = new ReflectionMethod($requestFactory($this->connector), 'filter');

        self::assertFalse(
            $method->isPublic(),
            'строковый filter() собирает ключи, которых у сущности может не быть — наружу только фильтр-объект'
        );
    }

    public function testTraitDeclaresFilterAsProtected(): void
    {
        self::assertTrue((new ReflectionMethod(HasFilterQuery::class, 'filter'))->isProtected());
    }
}
