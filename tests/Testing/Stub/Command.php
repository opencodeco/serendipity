<?php

declare(strict_types=1);

namespace Serendipity\Test\Testing\Stub;

use DateTimeImmutable;
use Serendipity\Domain\Entity\Entity;

class Command extends Entity
{
    /**
     * @SuppressWarnings(ExcessiveParameterList)
     * @SuppressWarnings(ShortVariable)
     */
    public function __construct(
        public readonly string $email,
        public readonly string $ipAddress,
        public readonly DateTimeImmutable $signupDate,
        public readonly ?string $firstName = null,
        public readonly ?string $lastName = null,
        public readonly ?string $address = null,
        public readonly ?string $city = null,
        public readonly ?string $state = null,
        public readonly ?string $zip = null,
        public readonly ?string $phone = null,
        public readonly ?string $leadId = null,
        public readonly ?string $sex = null,
        public readonly ?string $birthday = null,
        public readonly ?DateTimeImmutable $dob = null,
        public readonly ?string $c1 = null,
        public readonly ?string $hid = null,
        public readonly ?string $carMake = null,
        public readonly ?string $carModel = null,
        public readonly ?int $carYear = null,
    ) {
    }
}
