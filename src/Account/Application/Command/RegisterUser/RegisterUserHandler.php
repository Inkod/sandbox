<?php

declare(strict_types=1);

namespace App\Account\Application\Command\RegisterUser;

use App\Account\Application\Command\RegisterUser\Exception\UnableToRegisterUserEmailAlreadyUsedException;
use App\Account\Domain\Model\EmailAddress;
use App\Account\Domain\Model\User;
use App\Account\Domain\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class RegisterUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @throws UnableToRegisterUserEmailAlreadyUsedException
     */
    public function __invoke(RegisterUserInput $input): RegisterUserOutput
    {
        $email = new EmailAddress($input->email);

        if (null !== $this->userRepository->findByEmail($email)) {
            throw new UnableToRegisterUserEmailAlreadyUsedException();
        }

        $user = new User($email, $input->hashedPassword);
        $this->userRepository->save($user);

        return new RegisterUserOutput($user);
    }
}
