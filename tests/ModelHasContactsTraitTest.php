<?php

declare(strict_types=1);

namespace Manzadey\tests;

use Manzadey\SaloonAmoCrm\Modules\Contact\ContactModel;
use Manzadey\SaloonAmoCrm\Modules\Contact\Requests\HasContacts;
use Manzadey\SaloonAmoCrm\Modules\Model;
use PHPUnit\Framework\TestCase;

class ModelHasContactsTraitTest extends TestCase
{
    private function subject(): Model
    {
        return new class () extends Model {
            use HasContacts;
        };
    }

    public function testAddContactTwiceKeepsDistinctContacts(): void
    {
        $model = $this->subject();
        $model->addContact(new ContactModel(['id' => 1, 'is_main' => true]));
        $model->addContact(new ContactModel(['id' => 2, 'is_main' => false]));

        $ids = array_map(
            static fn (ContactModel $contact): ?int => $contact->id(),
            $model->contacts(),
        );

        self::assertSame([1, 2], $ids);
    }
}
