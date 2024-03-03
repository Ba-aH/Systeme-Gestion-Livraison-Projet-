<?php

namespace App\Controller;
use DateTime; 

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Client;
use App\Entity\Coursier;
use App\Entity\LivraisonHistory;

use App\Entity\Livraison;
use App\Entity\StatutLivraison;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ClientRepository;
use App\Repository\LivraisonHistoryRepository;
use App\Repository\StatutLivraisonRepository;
use App\Repository\LivraisonRepository;
use App\Repository\AdresseRepository ;
use App\Repository\TournerRepository ;
use App\Repository\CoursierRepository ;
use App\Repository\StatutCoursierRepository;
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

    #[Route('/coursiers', name: 'coursiers', methods: ['GET'])]
    public function coursiers(CoursierRepository $coursierRepository): Response
    {
        return $this->render('administrator/coursier.html.twig', [
            'coursiers' => $coursierRepository->findAll(),
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
          
            $entityManager->flush();
            $entityManager->flush();
      
            return $this->redirectToRoute('app_colis_index');

           } else {
            return $this->redirectToRoute('app_colis_index');
           }
    }


    #[Route('/submit_form_coursier', name: 'submit_form_coursier', methods: ['POST'])]
    public function submit_form_coursier(CoursierRepository $coursierRepository,Request $request ,EntityManagerInterface $entityManager): Response
    {
        $id = $request->request->get('id');
        $user = $entityManager->getRepository(Coursier::class)->findOneBy(['id' => $id]);

        if ($user) {
       
            $user->setEmail($request->request->get('email'));
            $user->setUsername($request->request->get('username'));
            $user->setPassword($request->request->get('password'));
            $user->setPhone($request->request->get('phone'));
            $user->setNom($request->request->get('nom'));
            $user->setPrenom($request->request->get('prenom'));

            $entityManager->flush();
            $entityManager->flush();
         
            return $this->redirectToRoute('coursiers');

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
                        $mod_stat->setStatusTitle('en attente');
                        $mod_stat->setStatusDateModifier($now);
                        $livraison->setTourner(null);
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
         
    #[Route('/delete/{id}', name: 'app_client_delete', methods: ['POST'])]
    public function delete(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$client->getId(), $request->request->get('_token'))) {
            $entityManager->remove($client);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_colis_index');

    }
    #[Route('/deletecoursier/{id}', name: 'deletecoursier', methods: ['POST'])]
    public function deletecoursier(Request $request, Coursier $coursier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$coursier->getId(), $request->request->get('_token'))) {
            $entityManager->remove($coursier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('coursiers');

    }


//     #[Route('/history', name: 'history')]
//     public function history(EntityManagerInterface $entityManager,LivraisonHistoryRepository $LivraisonHistoryRepository): Response
//     {
//         $liv_hist=$LivraisonHistoryRepository->findAll();
//         $livraisons = [];

//   foreach ($liv_hist as $item) {
//             $livraisons[] = $item->getLivraison();
//         }
//         return $this->render('administrator/history.html.twig', [
//             'livraisons' => $livraisons, 
//         ]);
//     }
   

    //a #[Route('/history', name: 'history')]
    // public function history(CoursierRepository $CoursierRepository,EntityManagerInterface $entityManager,LivraisonHistoryRepository $LivraisonHistoryRepository): Response
    // {  $coursiers=$CoursierRepository->findAll();
      
    //     return $this->render('administrator/history.html.twig', [
    //         'coursiers' => $coursiers, 
    //     ]);
    // }

               
}




  // #[Route('/filtrer', name: 'filtrer', methods: ['POST']) ]
    // public function filtrer(EntityManagerInterface $entityManager,LivraisonHistoryRepository $LivraisonHistoryRepository,Request $request): Response
    // {  
         // $date = $request->request->get('date');
// $region = $request->request->get('region');
// $coursier = $request->request->get('coursier');

// $dql = "SELECT lh 
// FROM App\Entity\LivraisonHistory lh
// LEFT JOIN lh.livraison livraison
// LEFT JOIN livraison.coursier coursier
// LEFT JOIN livraison.adresse adresse
// LEFT JOIN adresse.region region
// WHERE lh.date = :date
// AND coursier = :coursier
// AND region = :region";

// $query = $entityManager->createQuery($dql);
// $query->setParameter('date', $date)
// ->setParameter('coursier', $coursier)
// ->setParameter('region', $region);

// $result= $query->getResult();
// $jsonResult = json_encode($result);
// return $this->json($jsonResult);
    // }
    
        //     $data = [];
        // foreach (    $liv as $livraison) {
          
        
        //         $data[] = [
        //             'id' => $livraison->getId(),
                   
        //         ];
            
        // }
        //     return $this->json($data);
        // } catch (\Exception $e) {
        //     // Log the error or return a meaningful error response
        //     return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        // }