<?php

declare(strict_types=1);

namespace Manzadey\tests;

use DateTimeImmutable;
use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Contact\Requests\ContactCreateRequest;
use Manzadey\SaloonAmoCrm\Modules\Lead\LeadModel;
use Manzadey\SaloonAmoCrm\Modules\Lead\Requests\LeadUpdateRequest;
use Manzadey\SaloonAmoCrm\Modules\Tag\Requests\TagAttachRequest;
use Manzadey\SaloonAmoCrm\Modules\Tag\Requests\TagCreateRequest;
use Manzadey\SaloonAmoCrm\Modules\Tag\TagReference;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ApiNamingTest extends TestCase
{
    /**
     * @return array<string, array{class-string, string}>
     */
    public static function cutNames(): array
    {
        return [
            'ContactCreateRequest::save' => [ContactCreateRequest::class, 'save'],
            'LeadUpdateRequest::addLead' => [LeadUpdateRequest::class, 'addLead'],
            'LeadUpdateRequest::addLeads' => [LeadUpdateRequest::class, 'addLeads'],
            'TagCreateRequest::tag' => [TagCreateRequest::class, 'tag'],
            'TagAttachRequest::model' => [TagAttachRequest::class, 'model'],
            'TagReference::updateLead' => [TagReference::class, 'updateLead'],
        ];
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('cutNames')]
    public function testNonCanonicalNamesAreGone(string $class, string $method): void
    {
        self::assertFalse(
            method_exists($class, $method),
            sprintf('%s::%s() срезан в пользу канонических add()/addMany()/send()', $class, $method)
        );
    }

    /**
     * @return array<string, array{class-string, string}>
     */
    public static function canonicalNames(): array
    {
        return [
            'LeadUpdateRequest::add' => [LeadUpdateRequest::class, 'add'],
            'LeadUpdateRequest::addMany' => [LeadUpdateRequest::class, 'addMany'],
            'TagCreateRequest::add' => [TagCreateRequest::class, 'add'],
            'TagAttachRequest::add' => [TagAttachRequest::class, 'add'],
        ];
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('canonicalNames')]
    public function testCanonicalNamesExist(string $class, string $method): void
    {
        self::assertTrue(method_exists($class, $method), sprintf('%s::%s() ожидается', $class, $method));
    }

    public function testAddManyPutsEveryModelIntoBody(): void
    {
        $connector = new MainConnector(
            'test.amocrm.ru',
            static fn () => new \Saloon\Http\Auth\AccessTokenAuthenticator('token', 'refresh', new DateTimeImmutable()),
        );

        $request = (new LeadUpdateRequest($connector))->addMany(
            LeadModel::make()->setId(1),
            LeadModel::make()->setId(2),
        );

        self::assertSame(
            [['id' => 1], ['id' => 2]],
            $request->body()->all()
        );
    }
}
