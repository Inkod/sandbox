<?php

declare(strict_types=1);

namespace App\Account\Application\Command\RegisterUser;

use App\Account\Domain\Model\User;

final class RegisterUserOutput
{
    public function __construct(
        public readonly User $user,
    ) {
    }
}
