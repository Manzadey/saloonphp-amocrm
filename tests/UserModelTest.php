<?php

declare(strict_types=1);

namespace Manzadey\tests;

use Manzadey\SaloonAmoCrm\Modules\User\UserModel;
use PHPUnit\Framework\TestCase;

class UserModelTest extends TestCase
{
    public function testName(): void
    {
        $model = new UserModel();

        $this->assertNull($model->name());
        $this->assertSame($model, $model->setName('Ivan Ivanov'));
        $this->assertSame('Ivan Ivanov', $model->name());
    }
}
