<?php

declare(strict_types=1);

namespace App\Account\Domain\Model;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table(name: '`user`')]
final class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(type: 'user_id')]
    private UserId $id;

    #[ORM\Column(type: 'email', unique: true)]
    private EmailAddress $email;

    #[ORM\Column(type: 'text')]
    private string $password;

    public function __construct(EmailAddress $email, string $hashedPassword)
    {
        $this->id = UserId::generate();
        $this->email = $email;
        $this->password = $hashedPassword;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        // nothing to do
    }

    public function getUserIdentifier(): string
    {
        return $this->email->toString();
    }
}
