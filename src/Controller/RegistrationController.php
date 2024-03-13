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

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'select_register')]
    public function selectRegister(): Response
    {
        return $this->render('registration/select_register.html.twig');
    }

    #[Route('/register/coursier', name: 'app_register_coursier',methods: ['GET', 'POST'])]
    public function registerCoursier(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Coursier();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
       
        
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register_coursier.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/register/client', name: 'app_register_client',methods: ['GET', 'POST'])]
    public function registerClient(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Client();
        $form = $this->createForm(RegostrationClientFormType::class, $user);
        $form->handleRequest($request);

        $adresse = new Adresse();
        $formAdr = $this->createForm(AdresseFormType::class, $adresse);
        $formAdr->handleRequest($request);
        
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

            // $entityManager->persist($adresse);
            // $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register_client.html.twig', [
            'registrationForm' => $form->createView(),
            'adresseForm' => $formAdr->createView(),
        ]);
    }
    
}
