<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Entity\Client;
use App\Entity\Coursier;
use App\Form\RegistrationFormType;
use App\Form\AdresseFormType;
use App\Form\RegostrationClientFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use App\Security\EmailVerifier;
use App\Repository\CoursierRepository;
use App\Repository\ClientRepository;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'select_register')]
    public function selectRegister(): Response
    {
        return $this->render('registration/select_register.html.twig');
    }

    #[Route('/register/coursier', name: 'app_register_coursier',methods: ['GET', 'POST'])]
    public function registerCoursier(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,CoursierRepository $coursierRepository): Response
    {   $error=0;
        $user = new Coursier();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $email= $form->get('email')->getData() ;     
        $finduser =$coursierRepository->findOneBy(['email' => $email]);
        if($finduser){
            $error=1;
            return $this->render('registration/register_coursier.html.twig', [
                'registrationForm' => $form->createView(), 'error' => $error
            ]);
        }else{
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_USER']);
            $user->setDateAjout(new \DateTime());
            $entityManager->persist($user);
            $entityManager->flush();

            //generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('behantous@gmail.com', 'Baha'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            //do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }
}
return $this->render('registration/register_coursier.html.twig', [
    'registrationForm' => $form->createView(), 'error' => $error
]);
    }

    #[Route('/register/client', name: 'app_register_client',methods: ['GET', 'POST'])]
    public function registerClient(ClientRepository $clientRepository, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    { $error=0;
        $user = new Client();
        $form = $this->createForm(RegostrationClientFormType::class, $user);
        $form->handleRequest($request);
        $email= $form->get('email')->getData() ;     
        $finduser =$clientRepository->findOneBy(['email' => $email]);
        $adresse = new Adresse();
        $formAdr = $this->createForm(AdresseFormType::class, $adresse);
        $formAdr->handleRequest($request);
        if($finduser){
            $error=1;
            return $this->render('registration/register_client.html.twig', [
                'registrationForm' => $form->createView(),
                'adresseForm' => $formAdr->createView(),'error' => $error
            ]);}else{
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_USER']);
            $user->setDateAjout(new \DateTime());
            $entityManager->persist($user);
            $entityManager->flush();
            
            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('behantous@gmail.com', 'Baha'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            $entityManager->persist($adresse);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }}

        return $this->render('registration/register_client.html.twig', [
            'registrationForm' => $form->createView(),
            'adresseForm' => $formAdr->createView(),'error' => $error
        ]);
    }
    
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_login');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_login');
    }
}
