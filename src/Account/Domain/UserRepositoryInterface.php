<?php

declare(strict_types=1);

namespace App\Account\Domain;

use App\Account\Domain\Model\EmailAddress;
use App\Account\Domain\Model\User;

interface UserRepositoryInterface
{
    public function findByEmail(EmailAddress $email): ?User;

    public function save(User $user): void;
}
