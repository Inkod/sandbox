<?php

declare(strict_types=1);

namespace App\UserInterface\Web\Symfony\Controller\Account\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class LogoutController extends AbstractController
{
    #[Route(path: '/logout', name: 'account_logout_route')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
