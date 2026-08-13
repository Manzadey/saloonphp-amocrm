<?php

declare(strict_types=1);

namespace Manzadey\tests;

use DateTimeImmutable;
use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Contact\ContactOrderField;
use Manzadey\SaloonAmoCrm\Modules\Contact\Requests\ContactListRequest;
use Manzadey\SaloonAmoCrm\Modules\CustomField\CustomFieldOrderField;
use Manzadey\SaloonAmoCrm\Modules\CustomField\Requests\CustomFieldListRequest;
use Manzadey\SaloonAmoCrm\Modules\Lead\LeadOrderField;
use Manzadey\SaloonAmoCrm\Modules\Lead\Requests\LeadListRequest;
use Manzadey\SaloonAmoCrm\Modules\Note\NoteOrderField;
use Manzadey\SaloonAmoCrm\Modules\Note\Requests\NoteListRequest;
use Manzadey\SaloonAmoCrm\Modules\Task\Requests\TaskListRequest;
use Manzadey\SaloonAmoCrm\Modules\Task\TaskOrderField;
use Manzadey\SaloonAmoCrm\Query\OrderField;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Saloon\Http\Auth\AccessTokenAuthenticator;
use Saloon\Http\Request;

class HasOrderQueryTest extends TestCase
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
     * Набор полей сортировки у каждой сущности свой — сверено с офиц. докой amoCRM.
     * Единый енам на все сущности пропускал сортировку задач по `updated_at` и сделок
     * по `sort`, которые API отклоняет.
     *
     * @return array<string, array{callable(MainConnector): Request, class-string<OrderField>, list<string>}>
     */
    public static function entities(): array
    {
        return [
            'leads' => [
                static fn (MainConnector $c): Request => new LeadListRequest($c),
                LeadOrderField::class,
                ['created_at', 'updated_at', 'id'],
            ],
            'contacts' => [
                static fn (MainConnector $c): Request => new ContactListRequest($c),
                ContactOrderField::class,
                ['updated_at', 'id'],
            ],
            'tasks' => [
                static fn (MainConnector $c): Request => new TaskListRequest($c),
                TaskOrderField::class,
                ['created_at', 'complete_till', 'id'],
            ],
            'notes' => [
                static fn (MainConnector $c): Request => new NoteListRequest($c, 'leads'),
                NoteOrderField::class,
                ['updated_at', 'id'],
            ],
            'custom fields' => [
                static fn (MainConnector $c): Request => new CustomFieldListRequest($c, 'leads'),
                CustomFieldOrderField::class,
                ['sort', 'id'],
            ],
        ];
    }

    /**
     * @param callable(MainConnector): Request $factory
     * @param class-string<OrderField>         $enum
     * @param list<string>                     $documentedFields
     */
    #[DataProvider('entities')]
    public function testEnumHoldsExactlyTheDocumentedFields(
        callable $factory,
        string $enum,
        array $documentedFields,
    ): void {
        self::assertTrue(is_subclass_of($enum, OrderField::class), "$enum должен реализовать OrderField");
        self::assertSame(
            $documentedFields,
            array_map(static fn (OrderField $case): string => (string) $case->value, $enum::cases()),
        );
    }

    /**
     * @param callable(MainConnector): Request $factory
     * @param class-string<OrderField>         $enum
     * @param list<string>                     $documentedFields
     */
    #[DataProvider('entities')]
    public function testLatestAndOldestDefaultToId(
        callable $factory,
        string $enum,
        array $documentedFields,
    ): void {
        self::assertSame(['id' => 'desc'], $factory($this->connector)->latest()->query()->get('order'));
        self::assertSame(['id' => 'asc'], $factory($this->connector)->oldest()->query()->get('order'));
    }

    /**
     * @param callable(MainConnector): Request $factory
     * @param class-string<OrderField>         $enum
     * @param list<string>                     $documentedFields
     */
    #[DataProvider('entities')]
    public function testEveryDocumentedFieldReachesTheQuery(
        callable $factory,
        string $enum,
        array $documentedFields,
    ): void {
        foreach ($enum::cases() as $case) {
            self::assertSame(
                [$case->value => 'desc'],
                $factory($this->connector)->latest($case)->query()->get('order'),
            );
        }
    }

    public function testRemoveOrderDropsTheParameter(): void
    {
        $request = (new LeadListRequest($this->connector))->latest(LeadOrderField::CREATED_AT)->removeOrder();

        self::assertNull($request->query()->get('order'));
    }

    public function testUnionEnumIsGone(): void
    {
        self::assertFalse(
            enum_exists('Manzadey\SaloonAmoCrm\Enum\QueryOrderFieldEnum'),
            'QueryOrderFieldEnum разделён на пер-сущностные енамы'
        );
    }
}
