<?php

declare(strict_types=1);

namespace App\Account\Application\Command\RegisterUser\Exception;

final class UnableToRegisterUserEmailAlreadyUsedException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Unable to register user, email address already used for another user.');
    }
}
