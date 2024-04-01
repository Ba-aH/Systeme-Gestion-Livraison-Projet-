<?php

namespace App\Controller;

use App\Entity\Administrateur;
use App\Entity\Client;
use App\Entity\Coursier;
use App\Entity\ResetPassword;
use App\Form\ResetPasswordFormType;
use App\Repository\ClientRepository;
use App\Repository\CoursierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ResetPasswordRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

class LoginController extends AbstractController
{
    private $tokenStorage;
    private $serializer;
    

    public function __construct(TokenStorageInterface $tokenStorage,SerializerInterface $serializer)
    {
        $this->tokenStorage = $tokenStorage;
        $this->serializer = $serializer;
        
    }
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

    #[Route('/after', name: 'after_login_app')]
    public function afterLogin(AuthenticationUtils $authenticationUtils): Response
    {   
        $token = $this->tokenStorage->getToken();
        $currentUser = $token->getUser();
        $error = $authenticationUtils->getLastAuthenticationError();
        
        if ($currentUser instanceof Client) {
            return $this->redirectToRoute('clientProfile');
        }
        if ($currentUser instanceof Coursier) {
            return $this->redirectToRoute('coursierProfile');
        }   
        if ($currentUser instanceof Administrateur) {
            return $this->redirectToRoute('dashboard');
        }          
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout()
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    #[Route('/forgot-password', name: 'forgotPassword')]
    public function forgotPassword()
    {
        return $this->render('login/forgot_password.html.twig');
    }

    #[Route('/reset-password', name: 'resetPass')]
    public function resetPassword(Request $request, MailerInterface $mailer, ClientRepository $clientRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, CoursierRepository $coursierRepository,ResetPasswordRepository $resetPassword): Response
    {
        $email = $request->query->get('email');
        $user = $clientRepository->findOneBy(['email' => $email]);
        if (empty($user)){
           $user = $coursierRepository->findOneBy(['email' => $email]);   
        }
        
        if ($user) {
            // Generate random password
            $pin = sprintf('%06d', mt_rand(0, 999999)); 
            $resetPasswordRequest = new ResetPassword();
            $resetPasswordRequest->setCodePIN($pin);
            $resetPasswordRequest->setEmail($email);
            $resetPasswordRequest->setDefaultExpiredAt();
            $entityManager->persist($resetPasswordRequest);
            $entityManager->flush();
            

            $email = (new Email())
                ->from('your@example.com')
                ->to($email)
                ->subject('Your new password')
                ->text(sprintf('Reset password PIN: %s', $pin));    
            $mailer->send($email);
            

            return $this->render('login/pin_insert.html.twig', [
                
            ]);
        }

        return $this->render('login/reset_password_not_found.html.twig');
    }

    #[Route('/verifCodePin', name: 'verifCodePin')]
    public function CheckPin(Request $request,ResetPasswordRepository $resetPassword,)
    {
        $codePIN = $request->query->get('pin');
        $verif = $resetPassword->findOneBy(['codePIN' => $codePIN]);
        $form = $this->createForm(ResetPasswordFormType::class);
        if (!empty($verif)){
        return $this->render('login/reset_password.html.twig', [
            'ResetPasswordForm' => $form->createView(),
            'pin' => $codePIN,
        ]);
        }
        return $this->render('login/reset_password_not_found.html.twig');
    }

    #[Route('/change-password', name: 'change-password')]
    public function ChangePassword(Request $request, AuthenticationUtils $authenticationUtils, EntityManagerInterface $entityManager,ResetPasswordRepository $resetPassword, ClientRepository $clientRepository, UserPasswordHasherInterface $userPasswordHasher,CoursierRepository $coursierRepository)
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();
        $codePIN = $request->query->get('pin');
        $resetPasswordRequest = $resetPassword->findOneBy(['codePIN' => $codePIN]);
        $email= $resetPasswordRequest->getEmail();
        $user = $clientRepository->findOneBy(['email' => $email]);
        if (!$user) {
            $user = $coursierRepository->findOneBy(['email' => $email]);
        }
        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password=$form->get('password')->getData();
            $cpassword=$form->get('confirmPassword')->getData();
            if ($password==$cpassword){
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $entityManager->persist($user);
                $entityManager->flush();
            
                return $this->render('login/index.html.twig', [
                    'controller_name' => 'LoginController',
                    'last_username' => $lastUsername,
                    'error'         => $error,
                ]);
            }
            else{
                $codePIN = $request->query->get('pin');
                $form = $this->createForm(ResetPasswordFormType::class);
                return $this->render('login/reset_password.html.twig', [
                    'ResetPasswordForm' => $form->createView(),
                    'pin' => $codePIN,
                    'error' =>'le mot de passe et la confirmation du mot de passe ne correspondent pas',
                ]);
            }
        }
        return $this->render('login/reset_password_not_found.html.twig');
    }

    #[Route('/deleteExpiredPins', name: 'deleteExpiredPins')]
    public function deleteExpired(
        Request $request,
        EntityManagerInterface $entityManager,
        ResetPasswordRepository $resetPasswordRepository,
        ClientRepository $clientRepository,
        CoursierRepository $coursierRepository
    )
    {
        // Execute the method to delete expired reset requests
        $deletedCount = $resetPasswordRepository->deleteExpiredRequests();

        // Optionally, you can handle the result
        if ($deletedCount > 0) {
            // Log or return a response indicating successful deletion
            return new Response("Expired reset requests have been successfully deleted.");
        } else {
            // Optionally handle the case where no expired requests were found
            return new Response("No expired reset requests found to delete.");
        }
    }

  

}
