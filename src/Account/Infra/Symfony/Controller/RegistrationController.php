<?php

declare(strict_types=1);

namespace App\Account\Infra\Symfony\Controller;

use App\Account\Application\Command\RegisterUser\Exception\UnableToRegisterUserEmailAlreadyUsedException;
use App\Account\Application\Command\RegisterUser\RegisterUserHandler;
use App\Account\Application\Command\RegisterUser\RegisterUserInput;
use App\Account\Infra\Symfony\Form\RegistrationFormType;
use App\Account\Infra\Symfony\Security\AppAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
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
        private readonly RegisterUserHandler $handler,
        private readonly UserAuthenticatorInterface $userAuthenticator,
        private readonly AppAuthenticator $authenticator,
        PasswordHasherFactoryInterface $passwordHasherFactory,
    ) {
        $this->passwordHasher =  $passwordHasherFactory->getPasswordHasher('auto');
    }

    #[Route('/register', name: 'account_register_route', methods: ['GET', 'POST'])]
    public function register(Request $request): Response
    {
        $form = $this->createForm(RegistrationFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // TODO: Use bus to handle it
                $output = $this->handler->__invoke(new RegisterUserInput(
                    $form->get('email')->getData(),
                    $this->passwordHasher->hash($form->get('plainPassword')->getData()),
                ));
            } catch (UnableToRegisterUserEmailAlreadyUsedException $e) {
                $form->addError(new FormError('There is already an account with this email.'));

                return $this->renderResponseWithView($form);
            }

            return $this->userAuthenticator->authenticateUser(
                $output->user,
                $this->authenticator,
                $request,
            );
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
