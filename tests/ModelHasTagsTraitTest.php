<?php

declare(strict_types=1);

namespace Manzadey\tests;

use Manzadey\SaloonAmoCrm\Modules\Model;
use Manzadey\SaloonAmoCrm\Modules\Tag\Requests\HasTags;
use Manzadey\SaloonAmoCrm\Modules\Tag\TagModel;
use PHPUnit\Framework\TestCase;

class ModelHasTagsTraitTest extends TestCase
{
    protected $classWithTrait;

    protected function setUp(): void
    {
        $this->classWithTrait = new class() extends Model {
            use HasTags;
        };
    }

    public function testAddOneTag(): void
    {
        $this->classWithTrait->addTag(new TagModel(['name' => 'tag_one']));

        $this->assertCount(1, $this->classWithTrait->tags());
    }

    public function testAddManyTags(): void
    {
        $tags = [
            'tag1',
            'tag2',
            'tag3',
        ];

       foreach ($tags as $tag) {
           $this->classWithTrait->addTag(new TagModel(['name' => $tag]));
       }

        $this->assertCount(3, $this->classWithTrait->tags());
    }

    public function testClearTags(): void
    {
        $this->assertCount(0, $this->classWithTrait->tags());

        $this->classWithTrait->addTag(new TagModel(['name' => 'tag_one']));
        $this->assertCount(1, $this->classWithTrait->tags());

        $this->classWithTrait->clearTags();
        $this->assertCount(0, $this->classWithTrait->tags());
    }

    public function testAddTagWithNotEmptyEmbedded(): void
    {
        $this->classWithTrait->add('_embedded', $data = [
            'test_data' => [
                'test_key' => 'test_value'
            ]
        ]);
        $this->assertEquals('test_value', $data['test_data']['test_key']);

        $this->classWithTrait->addTag(new TagModel(['name' => 'tag_one']));
        $this->assertEquals('test_value', $data['test_data']['test_key']);
        $this->assertCount(1, $this->classWithTrait->tags());

        $this->classWithTrait->clearTags();
        $this->assertEquals('test_value', $data['test_data']['test_key']);
        $this->assertCount(0, $this->classWithTrait->tags());
    }
}