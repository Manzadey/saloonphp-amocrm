<?php

declare(strict_types=1);

namespace Manzadey\tests;

use Manzadey\SaloonAmoCrm\Modules\Account\AccountWithQueryEnum;
use PHPUnit\Framework\TestCase;

class AccountWithQueryEnumTest extends TestCase
{
    public function testCaseValues(): void
    {
        $this->assertSame('amojo_id', AccountWithQueryEnum::AMOJO_ID->value);
        $this->assertSame('amojo_rights', AccountWithQueryEnum::AMOJO_RIGHTS->value);
        $this->assertSame('users_groups', AccountWithQueryEnum::USERS_GROUPS->value);
        $this->assertSame('task_types', AccountWithQueryEnum::TASK_TYPES->value);
        $this->assertSame('version', AccountWithQueryEnum::VERSION->value);
        $this->assertSame('entity_names', AccountWithQueryEnum::ENTITY_NAMES->value);
        $this->assertSame('datetime_settings', AccountWithQueryEnum::DATETIME_SETTINGS->value);
        $this->assertSame('drive_url', AccountWithQueryEnum::DRIVE_URL->value);
        $this->assertSame('is_api_filter_enabled', AccountWithQueryEnum::IS_API_FILTER_ENABLED->value);
        $this->assertSame('invoices_settings', AccountWithQueryEnum::INVOICES_SETTINGS->value);
    }

    public function testCasesCount(): void
    {
        $this->assertCount(10, AccountWithQueryEnum::cases());
    }

    public function testFromValue(): void
    {
        $this->assertSame(
            AccountWithQueryEnum::AMOJO_ID,
            AccountWithQueryEnum::from('amojo_id')
        );
    }
}
