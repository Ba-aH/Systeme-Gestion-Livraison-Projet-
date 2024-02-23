<?php

namespace App\Controller;
use App\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ClientRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
#[Route('/administrator')]
class AdministratorController extends AbstractController
{
    #[Route('/index', name: 'app_administrator')]
    public function index(): Response
    {
        return $this->render('administrator/index.html.twig', [
            'controller_name' => 'AdministratorController',
        ]);
    }

    #[Route('/clients', name: 'app_colis_index', methods: ['GET'])]
    public function index1(ClientRepository $clientRepository): Response
    {
        return $this->render('administrator/coursier_list.html.twig', [
            'clients' => $clientRepository->findAll(),
        ]);
        
    }
    #[Route('/submit_form', name: 'submit_form', methods: ['POST'])]
    public function submit_form(ClientRepository $clientRepository,Request $request ,EntityManagerInterface $entityManager): Response
    {
        $id = $request->request->get('id');
        $user = $entityManager->getRepository(Client::class)->findOneBy(['id' => $id]);

     
            // Update the Livraison entity with the provided data

            $user->setEmail($request->request->get('email'));
            $user->setUsername($request->request->get('username'));
            $user->setPassword($request->request->get('password'));
            $user->setPhone($request->request->get('phone'));
            $user->setNom($request->request->get('nom'));
            $user->setPrenom($request->request->get('prenom'));
          
           
 
            // Persist the changes to the database
            $entityManager->flush();
            $entityManager->flush();
            $clients=$clientRepository->findAll();
          
            // Redirect or render a response as needed
            return $this->render('administrator/coursier_list.html.twig', [
              
                'clients' => $clients
            ]);
       



       
    
}
}