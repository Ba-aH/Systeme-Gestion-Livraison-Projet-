<?php

namespace App\Controller;
use DateTime; 

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Client;
use App\Entity\Livraison;
use App\Entity\StatutLivraison;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ClientRepository;
use App\Repository\StatutLivraisonRepository;
use App\Repository\LivraisonRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

use Doctrine\Persistence\ManagerRegistry;



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

        if ($user) {
            $now = new \DateTimeImmutable();
            $user->setEmail($request->request->get('email'));
            $user->setUsername($request->request->get('username'));
            $user->setPassword($request->request->get('password'));
            $user->setPhone($request->request->get('phone'));
            $user->setNom($request->request->get('nom'));
            $user->setPrenom($request->request->get('prenom'));
            $user->setDateModification($now);
            $entityManager->flush();
            $entityManager->flush();
            $clients=$clientRepository->findAll();
            return $this->redirectToRoute('app_colis_index');

           } else {
            return $this->redirectToRoute('app_colis_index');
           }
    }
    #[Route('/echec', name: 'echec', methods: ['get'])]
    public function adliv(LivraisonRepository $livraisonRepository,StatutLivraisonRepository $statut ,ManagerRegistry $registry,ClientRepository $clientRepository,EntityManagerInterface $entityManager): Response
    {
        $livraisonsEchec = $statut->findBy(['status_title' => 'echec']);
        $resultArray=[];
        foreach ($livraisonsEchec as $livraison) {
              
        $livraisonn= $livraison->getLivraison();
        $resultArray[] = $livraisonn->getId();

        }
        $livraisons = $entityManager->getRepository(Livraison::class)->findBy(['id' => $resultArray]);


        return $this->render('administrator/echec.html.twig', [
           
            'livraisonsEchec'=> $livraisons,
        ]);



         }
         #[Route('/edit_byadmin', name: 'edit_byadmin', methods: ['POST'])]
         public function edit_byadmin( Request $request ,ClientRepository $clientRepository,EntityManagerInterface $entityManager,LivraisonRepository $livraisonRepository): Response
         {
             
             $livraisonIdd = $request->request->get('ids');
             // Check if the Livraison ID exists in the request data
          
                 // Retrieve the Livraison ID
                
                 $now = new \DateTimeImmutable();
           
                 $livraison = $entityManager->getRepository(Livraison::class)->find($livraisonIdd);
                 $dateLivraisonString = $request->request->get('date'); // Assuming 'date_livraison' is the name of your form field
             
                 // Convert the string to a DateTime object
                 $dateLivraison = DateTime::createFromFormat('Y-m-d\TH:i', $dateLivraisonString);
                 // Check if the Livraison entity exists
                 if ($livraison) {
                     // Update the Livraison entity with the provided data
                     $livraison->setLivraisonDate($dateLivraison);

                     $mod_stat = $entityManager->getRepository(StatutLivraison::class)->findOneBy(['livraison' => $livraisonIdd]);
                     if ($mod_stat) {
                        $mod_stat->setStatusTitle('en attend');
                        $mod_stat->setStatusDateModifier($now);
                       }
                     // Persist the changes to the database
                     $entityManager->flush();
                     $clients=$clientRepository->findAll();
                     // Redirect or render a response as needed
                   
                     return $this->redirectToRoute('echec');
                 } else {
                     // Handle the case where the Livraison entity is not found
                     // You can return an error response or handle it based on your application logic
                     return $this->redirectToRoute('echec');
                 }
             
         }
}