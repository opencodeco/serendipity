<?php

declare(strict_types=1);

namespace Serendipity\Test\Domain\Support\Reflective\Factory;

use PHPUnit\Framework\TestCase;
use Serendipity\Domain\Support\Reflective\Factory\Ruler;
use Serendipity\Test\Testing\Stub\Command;

/**
 * @internal
 */
class RulerTest extends TestCase
{
    public function testShouldCreateRules(): void
    {
        $ruler = new Ruler();
        $rules = $ruler->ruleset(Command::class);
        $this->assertCount(19, $rules->all());
        $this->assertContains('required', $rules->get('email'));
        $this->assertContains('required', $rules->get('ip_address'));
        $this->assertContains('required', $rules->get('signup_date'));
        $this->assertContains('required', $rules->get('first_name'));
        $this->assertContains('required', $rules->get('password'));
        $this->assertContains('sometimes', $rules->get('address'));
        $this->assertContains('sometimes', $rules->get('city'));
        $this->assertContains('sometimes', $rules->get('state'));
        $this->assertContains('sometimes', $rules->get('zip'));
        $this->assertContains('sometimes', $rules->get('phone'));
        $this->assertContains('sometimes', $rules->get('lead_id'));
        $this->assertContains('sometimes', $rules->get('birthday'));
        $this->assertContains('sometimes', $rules->get('dob'));
        $this->assertContains('sometimes', $rules->get('c_1'));
        $this->assertContains('sometimes', $rules->get('hid'));
        $this->assertContains('sometimes', $rules->get('car_make'));
        $this->assertContains('sometimes', $rules->get('car_model'));
        $this->assertContains('sometimes', $rules->get('car_year'));
    }
}
