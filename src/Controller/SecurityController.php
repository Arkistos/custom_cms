<?php

namespace App\Controller;

use App\Form\LoginFormType;
use App\Form\ResetPasswordFormType;
use App\Repository\UserRepository;
use App\Service\SendEmailService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        $loginForm = $this->createForm(LoginFormType::class);

        return $this->render('security/login.html.twig', [
            'loginForm' => $loginForm,
            'lastUsername' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/forgetPassword', name:'forget_password')]
    public function forgetPassword(
        Request $request,
        UserRepository $userRepository,
        SendEmailService $sendEmailService
    ):Response{
        $resetPasswordForm = $this->createForm(ResetPasswordFormType::class);

        $resetPasswordForm->handleRequest($request);

        if(!$resetPasswordForm->isSubmitted() || !$resetPasswordForm->isValid()){
            return $this->render('security/forgetPassword.html.twig', [
                'resetPasswordForm' => $resetPasswordForm
            ]);
        }

        $formData = $resetPasswordForm->getData();
        if($userRepository->findOneByEmail($formData['email'])){
            $sendEmailService->send(
                'no-reply@cms_custom.fr',
                $formData['email'],
                'Réinistialisez votre mot de passe',
                'lien pour réinitialiser le mot de passe'
            );
            $this->addFlash('success', 'Le lien permettant de réinitialiser votre mot de passe vous à été envoyé');
            return $this->redirectToRoute('login');
        }

        $this->addFlash('alert', 'Cet email n\'est pas inscrit');
        return $this->redirectToRoute('login');
    }
}
