<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Faker;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Serendipity\Test\Testing\Stub\Builtin;
use Serendipity\Test\Testing\Stub\Command;
use Serendipity\Test\Testing\Stub\EntityStub;
use Serendipity\Test\Testing\Stub\Type\SingleBacked;
use Serendipity\Testing\Faker\Faker;

/**
 * @internal
 */
class FakerTest extends TestCase
{
    public function testShouldMakeFakeEntityStub(): void
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

    public function testShouldMakeFakeBuiltin(): void
    {
        $faker = new Faker();
        $set = $faker->fake(Builtin::class);

        $this->assertIsString($set->at('string'));
        $this->assertIsInt($set->at('int'));
        $this->assertIsFloat($set->at('float'));
        $this->assertIsBool($set->at('bool'));
        $this->assertIsArray($set->at('array'));
        $this->assertNull($set->at('null'));
    }

    public function testShouldMakeFakeCommand(): void
    {
        $faker = new Faker();
        $set = $faker->fake(Command::class);

        $this->assertIsString($set->at('email'));
        $this->assertIsString($set->at('ip_address'));
        $this->assertInstanceOf(DateTimeImmutable::class, $set->at('signup_date'));
        $this->assertIsString($set->at('gender'));
        $this->assertIsString($set->get('first_name'));
    }
}
