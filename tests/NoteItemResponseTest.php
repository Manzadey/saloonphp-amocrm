<?php

declare(strict_types=1);

namespace Manzadey\tests;

use Manzadey\SaloonAmoCrm\Modules\Note\NoteModel;
use Manzadey\SaloonAmoCrm\Modules\Note\Responses\NoteItemResponse;
use PHPUnit\Framework\TestCase;

class NoteItemResponseTest extends TestCase
{
    private function responseWithJson(array $json): NoteItemResponse
    {
        $response = $this->getMockBuilder(NoteItemResponse::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['json'])
            ->getMock();

        $response->method('json')->willReturn($json);

        return $response;
    }

    public function testNoteReturnsNullForEmptyJson(): void
    {
        self::assertNull($this->responseWithJson([])->note());
    }

    public function testNoteReturnsModelForJson(): void
    {
        $note = $this->responseWithJson(['id' => 5, 'note_type' => 'common'])->note();

        self::assertInstanceOf(NoteModel::class, $note);
        self::assertSame(5, $note->getId());
    }
}
