<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Mime\Email;
class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
     public function index(AuthenticationUtils $authenticationUtils): Response
      {         // get the login error if there is one
         $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
         $lastUsername = $authenticationUtils->getLastUsername();

          return $this->render('login/index.html.twig', [
             'controller_name' => 'LoginController',
             'last_username' => $lastUsername,
             'error'         => $error,
          ]);
      }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout()
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    #[Route('/forgotPassword', name: 'forgotPassword')]
    public function forgor(MailerInterface $mailer,AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
         $lastUsername = $authenticationUtils->getLastUsername();

         $email = (new Email())
         ->from('rom@example.com')
         ->to('to@example.com')
         //->cc('cc@example.com')
         //->bcc('bcc@example.com')
         //->replyTo('fabien@example.com')
         //->priority(Email::PRIORITY_HIGH)
         ->subject('Time for Symfony Mailer!')
         ->text('Sending emails is fun again!');

        $mailer->send($email);

        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
            'last_username' => $lastUsername,
            'error'         => $error,
         ]);

    }
}
