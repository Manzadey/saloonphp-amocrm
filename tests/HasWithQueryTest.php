<?php

declare(strict_types=1);

namespace Manzadey\tests;

use DateTimeImmutable;
use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Account\AccountWith;
use Manzadey\SaloonAmoCrm\Modules\Account\Requests\AccountRequest;
use Manzadey\SaloonAmoCrm\Modules\Contact\ContactWith;
use Manzadey\SaloonAmoCrm\Modules\Contact\Requests\ContactListRequest;
use Manzadey\SaloonAmoCrm\Modules\Lead\LeadWith;
use Manzadey\SaloonAmoCrm\Modules\Lead\Requests\LeadListRequest;
use Manzadey\SaloonAmoCrm\Modules\Note\NoteWith;
use Manzadey\SaloonAmoCrm\Modules\Note\Requests\NoteListRequest;
use Manzadey\SaloonAmoCrm\Modules\User\Requests\UserListRequest;
use Manzadey\SaloonAmoCrm\Modules\User\UserWith;
use Manzadey\SaloonAmoCrm\Query\WithField;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class HasWithQueryTest extends TestCase
{
    private MainConnector $connector;

    protected function setUp(): void
    {
        $this->connector = new MainConnector(
            'test.amocrm.ru',
            static fn () => new \Saloon\Http\Auth\AccessTokenAuthenticator('token', 'refresh', new DateTimeImmutable()),
        );
    }

    public function testWithJoinsEnumValues(): void
    {
        $request = (new LeadListRequest($this->connector))->with([LeadWith::CONTACTS, LeadWith::SOURCE]);

        self::assertSame('contacts,source', $request->query()->get('with'));
    }

    public function testAddWithAccumulatesAndDeduplicates(): void
    {
        $request = (new LeadListRequest($this->connector))
            ->addWith(LeadWith::CONTACTS)
            ->addWith(LeadWith::LOSS_REASON)
            ->addWith(LeadWith::CONTACTS);

        self::assertSame('contacts,loss_reason', $request->query()->get('with'));
    }

    public function testTypedShortcutsUseTheEnum(): void
    {
        $request = (new LeadListRequest($this->connector))->withContacts()->withSource();

        self::assertSame('contacts,source', $request->query()->get('with'));
    }

    public function testAccountWithAllSendsEveryDocumentedValue(): void
    {
        $request = (new AccountRequest($this->connector))->withAll();

        self::assertSame(
            implode(',', array_map(static fn (AccountWith $case): string => $case->value, AccountWith::cases())),
            $request->query()->get('with')
        );
    }

    public function testAccountRequestUsesTheSharedTrait(): void
    {
        self::assertContains(
            \Manzadey\SaloonAmoCrm\Query\HasWithQuery::class,
            self::traitsOf(AccountRequest::class),
            'AccountRequest должен ходить через общий трейт, а не через свою реализацию with()'
        );
    }

    /**
     * @return array<string, array{class-string, class-string<WithField>, int}>
     */
    public static function withEnums(): array
    {
        return [
            'leads'    => [LeadListRequest::class, LeadWith::class, 7],
            'contacts' => [ContactListRequest::class, ContactWith::class, 3],
            'users'    => [UserListRequest::class, UserWith::class, 6],
            'notes'    => [NoteListRequest::class, NoteWith::class, 1],
            'account'  => [AccountRequest::class, AccountWith::class, 10],
        ];
    }

    /**
     * @param class-string            $requestClass
     * @param class-string<WithField> $enum
     */
    #[DataProvider('withEnums')]
    public function testEveryWithEnumMatchesTheDocumentedSet(
        string $requestClass,
        string $enum,
        int $documentedCases,
    ): void {
        self::assertTrue(is_subclass_of($enum, WithField::class), "$enum должен реализовать WithField");
        self::assertCount($documentedCases, $enum::cases());
        self::assertContains(
            \Manzadey\SaloonAmoCrm\Query\HasWithQuery::class,
            self::traitsOf($requestClass),
            "$requestClass должен подключать HasWithQuery"
        );
    }

    public function testMagicStringEnumIsGone(): void
    {
        self::assertFalse(
            enum_exists('Manzadey\SaloonAmoCrm\Modules\Account\AccountWithQueryEnum'),
            'AccountWithQueryEnum переименован в AccountWith'
        );
    }

    /**
     * @param  class-string $class
     * @return list<string>
     */
    private static function traitsOf(string $class): array
    {
        $traits = [];

        do {
            foreach (class_uses($class) ?: [] as $trait) {
                $traits[] = $trait;
                $traits = array_merge($traits, self::traitsOf($trait));
            }
        } while ($class = get_parent_class($class));

        return array_values(array_unique($traits));
    }
}
