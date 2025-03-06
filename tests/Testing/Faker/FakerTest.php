<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Faker;

use PHPUnit\Framework\TestCase;
use Serendipity\Test\Testing\Stub\EntityStub;
use Serendipity\Test\Testing\Stub\Type\SingleBacked;
use Serendipity\Testing\Faker\Faker;

class FakerTest extends TestCase
{
    public function testShouldMakeFakeInstance(): void
    {
        $faker = new Faker();
        $set = $faker->fake(EntityStub::class, ['is_active' => true]);

        $this->assertIsInt($set->at('id'));
        $this->assertIsFloat($set->at('price'));
        $this->assertIsString($set->at('name'));
        $this->assertTrue($set->at('is_active'));
        $this->assertEquals([], $set->at('more'));
        $this->assertNull($set->at('created_at'));
        $this->assertNull($set->at('no'));
        $this->assertIsArray($set->at('tags'));
        $this->assertEquals(SingleBacked::ONE, $set->at('enum'));
        $this->assertNull($set->at('foo'));
    }
}
