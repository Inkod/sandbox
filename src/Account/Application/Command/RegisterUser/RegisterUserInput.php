<?php

declare(strict_types=1);

namespace App\Account\Application\Command\RegisterUser;

final class RegisterUserInput
{
    public function __construct(
        public readonly string $email,
        public readonly string $hashedPassword,
    ) {}
}
