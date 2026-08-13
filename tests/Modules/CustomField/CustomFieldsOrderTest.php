<?php

declare(strict_types=1);

namespace Manzadey\tests\Modules\CustomField;

use DateTimeImmutable;
use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Contact\Requests\ContactCustomFieldsListRequest;
use Manzadey\SaloonAmoCrm\Modules\CustomField\CustomFieldOrderField;
use Manzadey\SaloonAmoCrm\Modules\CustomField\Requests\CustomFieldListRequest;
use Manzadey\SaloonAmoCrm\Modules\Lead\Requests\LeadCustomFieldsListRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Saloon\Http\Auth\AccessTokenAuthenticator;
use Saloon\Http\Request;

/**
 * Регрессия v0.8.0: удаление `SORT` из общего енама лишило все три
 * custom-fields-запроса единственного осмысленного поля сортировки.
 */
class CustomFieldsOrderTest extends TestCase
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
     * @return array<string, array{callable(MainConnector): Request}>
     */
    public static function customFieldsRequests(): array
    {
        return [
            'leads'    => [static fn (MainConnector $c): Request => new LeadCustomFieldsListRequest($c)],
            'contacts' => [static fn (MainConnector $c): Request => new ContactCustomFieldsListRequest($c)],
            'generic'  => [static fn (MainConnector $c): Request => new CustomFieldListRequest($c, 'leads')],
        ];
    }

    /**
     * @param callable(MainConnector): Request $factory
     */
    #[DataProvider('customFieldsRequests')]
    public function testOrdersBySortField(callable $factory): void
    {
        $request = $factory($this->connector);

        self::assertSame(['sort' => 'asc'], $request->oldest(CustomFieldOrderField::SORT)->query()->get('order'));
        self::assertSame(['sort' => 'desc'], $request->latest(CustomFieldOrderField::SORT)->query()->get('order'));
    }
}
