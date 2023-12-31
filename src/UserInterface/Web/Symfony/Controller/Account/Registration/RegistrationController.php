<?php

declare(strict_types=1);

namespace App\UserInterface\Web\Symfony\Controller\Account\Registration;

use App\Account\Application\Command\RegisterUser\Exception\UnableToRegisterUserEmailAlreadyUsedException;
use App\Account\Application\Command\RegisterUser\RegisterUserInput;
use App\Infra\Symfony\Messenger\CommandBus;
use App\Infra\Symfony\Security\AppAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

final class RegistrationController extends AbstractController
{
    private PasswordHasherInterface $passwordHasher;

    public function __construct(
        private readonly UserAuthenticatorInterface $userAuthenticator,
        private readonly AppAuthenticator $authenticator,
        private readonly CommandBus $commandBus,
        PasswordHasherFactoryInterface $passwordHasherFactory,
    ) {
        $this->passwordHasher = $passwordHasherFactory->getPasswordHasher('auto');
    }

    #[Route('/register', name: 'account_register_route', methods: ['GET', 'POST'])]
    public function register(Request $request): Response
    {
        $form = $this->createForm(RegistrationFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $output = $this->commandBus->handle(new RegisterUserInput(
                    $form->get('email')->getData(),
                    $this->passwordHasher->hash($form->get('plainPassword')->getData()),
                ));
            } catch (UnableToRegisterUserEmailAlreadyUsedException) {
                $form->addError(new FormError('There is already an account with this email.'));

                return $this->renderResponseWithView($form);
            }

            return $this->userAuthenticator->authenticateUser(
                $output->user,
                $this->authenticator,
                $request,
            ) ?: new RedirectResponse($this->generateUrl('account_login_route'));
        }

        return $this->renderResponseWithView($form);
    }

    private function renderResponseWithView(FormInterface $form): Response
    {
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
