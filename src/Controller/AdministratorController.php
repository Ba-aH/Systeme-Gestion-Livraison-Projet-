<?php

namespace App\Controller;

use App\Entity\Administrateur;
use App\Entity\Adresse;
use DateTime; 

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Client;
use App\Entity\Coursier;
use App\Entity\CoursierPositionHistory;
use App\Entity\LivraisonHistory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Livraison;
use App\Entity\StatutLivraison;
use App\Repository\AdministrateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ClientRepository;
use App\Repository\LivraisonHistoryRepository;
use App\Repository\StatutLivraisonRepository;
use App\Repository\CoursierPositionHistoryRepository;
use App\Repository\LivraisonRepository;
use App\Repository\AdresseRepository ;
use App\Repository\TournerRepository ;
use App\Repository\CoursierRepository ;
use App\Repository\StatutCoursierRepository;
use App\Service\MercureCookieGenerator;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Mime\Header\Headers;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;


#[Route('/administrator')]
class AdministratorController extends AbstractController
{
    private $tokenStorage;
    private $serializer;
    

    public function __construct(TokenStorageInterface $tokenStorage,SerializerInterface $serializer,StatutCoursierRepository $statutCoursierRepository,private ManagerRegistry $doctrine,private EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->serializer = $serializer;
        $this->doctrine = $doctrine;
    }
    


    #[Route('/adv', name: 'adv')]
    public function adv(MercureCookieGenerator $cookieGenerator): Response
    {
        $response = $this->render('adminv2/index.html.twig');
        $response ->headers->set('set-cookie', $cookieGenerator->generate($this->getUser()));
        return $response;
    }
   
    #[Route('/', name: 'dashboard')]
    public function dashboard(MercureCookieGenerator $cookieGenerator,EntityManagerInterface $entityManager): Response
    {
        $token = $this->tokenStorage->getToken();
        $currentUser = $token->getUser();
            
            if ($currentUser instanceof Administrateur) {
                $email = $currentUser->getEmail();
            }
            $now = new \DateTimeImmutable();
            $month = $now->format('m'); 
            $totale=0;
            $revenus = $entityManager->getRepository(StatutLivraison::class)->findBy(['status_title' =>'confirmé','status_date_ajout'=>$now]);
            foreach ($revenus as $item) {
            $frais=$item->getLivraison()->getFraisLivraison();
            $prix=$item->getLivraison()->getPrixTotaleLivraison();
                $totale=$totale+$frais+$prix;

            }

            $commande = $entityManager->getRepository(Livraison::class)->count([]);
            $clients = $entityManager->getRepository(Client::class)->count([]);
          
          $livs=$entityManager->getRepository(Livraison::class)->findBy(['livraison_date'=>$now]);
         // Assuming YourEntity represents the table in question
         $repository = $this->entityManager->getRepository(Coursier::class);

         $query = $repository->createQueryBuilder('c');

           $query->orderBy('c.id', 'DESC');
           $query->setMaxResults(5);

        $lastFiveCouriers = $query->getQuery()->getResult(); 

        $response = $this->render('adminv2/dashboard.html.twig',[
            'user'=> $currentUser, 'clients'=> $clients ,'commande'=> $commande,'revenus'=> $totale,'recentliv'=> $livs,'lastFiveCouriers'=>  $lastFiveCouriers
           
        ]);
        $response ->headers->set('set-cookie', $cookieGenerator->generate($this->getUser()));
        return $response;
    }

    #[Route('/extend', name: 'dashboardExtend')]
    public function dashboardExtend(
        LivraisonRepository $livraisonRepository,
        StatutLivraisonRepository $statutLivraisonRepository,
        SerializerInterface $serializer,
        AdresseRepository $adresseRepository
    ): JsonResponse {
        $ann = $statutLivraisonRepository->findBy(['status_title' => "annulée"]);
        $att = $statutLivraisonRepository->findBy(['status_title' => "en attente"]);
        $echec = $statutLivraisonRepository->findBy(['status_title' => "échec"]);
        $nb = 0;
        $livraisons = [];

        foreach ($ann as $item) {
            $liv = $item->getLivraison();
            $ref = $liv->getReference();
            $adr = $liv->getAddressId();
            $date = $liv->getLivraisonDate()->format('Y-m-d');
            $adresse = $adresseRepository->findOneBy(['id'=>$adr]);
            $region = $adresse->getRegion();
            $livraisons[] = [
                'reference' => $ref,
                'region' => $region,
                'date'=>$date,
            ];
            $nb++;
        }

        foreach ($att as $item) {
            $liv = $item->getLivraison();
            $ref = $liv->getReference();
            $date = $liv->getLivraisonDate()->format('Y-m-d');
            $adr = $liv->getAddressId();
            $adresse = $adresseRepository->findOneBy(['id'=>$adr]);
            $region = $adresse->getRegion();
            $livraisons[] = [
                'reference' => $ref,
                'region' => $region,
                'date'=>$date,
            ];
            $nb++;
        }

        foreach ($echec as $item) {
            $liv = $item->getLivraison();
            $ref = $liv->getReference();
            $adr = $liv->getAddressId();
            $adresse = $adresseRepository->findOneBy(['id'=>$adr]);
            $date = $liv->getLivraisonDate()->format('Y-m-d');
            $region = $adresse->getRegion();
            $livraisons[] = [
                'reference' => $ref,
                'region' => $region,
                'date'=>$date,
            ];
            $nb++;
        }

        $data = $serializer->serialize([
            'livraisons' => $livraisons,
            'nbLivraisons' => $nb,
        ], 'json', [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['livraisons'],
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);

        // Return JsonResponse
        return new JsonResponse($data, 200, [], true);
    }
    
    #[Route('/profile', name: 'profile')]
    public function profile(Request $request,EntityManagerInterface $entityManager): Response
    {  $error = $request->query->get('error', 0);
        $token = $this->tokenStorage->getToken();
        $currentUser = $token->getUser();
             return $this->render('adminv2/profile.html.twig',[
            'admin'=> $currentUser,'error'=> $error
        ]);
    }

    #[Route('/clients', name: 'clientss', methods: ['GET'])]
    public function index1(ClientRepository $clientRepository): Response
    {
        return $this->render('adminv2/clients.html.twig', [
            'clients' => $clientRepository->findAll(),
        ]);
        
    }

    #[Route('/coursiers', name: 'coursiers', methods: ['GET'])]
    public function coursiers(CoursierRepository $coursierRepository): Response
    {
        // return $this->render('administrator/coursier.html.twig', [
        //     'coursiers' => $coursierRepository->findAll(),
        // ]);
        return $this->render('adminv2/coursier.html.twig', [
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
      
            return $this->redirectToRoute('clientss');

           } else {
            return $this->redirectToRoute('clientss');
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
            return $this->redirectToRoute('coursiers');
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


        return $this->render('adminv2/liv_ech.html.twig', [
           
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
                 $dateLivraison = DateTime::createFromFormat('Y-m-d', $dateLivraisonString);
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

                    
                     $entityManager->flush();
                     $clients=$clientRepository->findAll();
              
                   
                     return $this->redirectToRoute('echec');
                 } else {
                    
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



    #[Route('/updateadamin', name: 'updateadamin',methods: ['POST'])]
    public function updateProfileadmin(Request $request, EntityManagerInterface $entityManager, AdministrateurRepository $administrateurRepository): Response
    {
            $token = $this->tokenStorage->getToken();
            $currentUser = $token->getUser();
            
            if ($currentUser instanceof Administrateur) {
                $dateAjout = $currentUser->getDateAjout()->format('Y-m-d');
                $id = $currentUser->getId();
                $user =$administrateurRepository->findOneBy(['id' => $id]);
                $prenom = $request->get('prenom');
                if ($prenom !== null) {
                    $prenom = is_string($prenom) ? $prenom : '';
                    $user->setPrenom($prenom);
                }
                $username = $request->get('username');
                if ($username !== null) {
                    $username = is_string($username) ? $username : '';
                    $user->setUsername($username);
                }
                $user->setNom($request->get('nom'));

                $phone = $request->get('phone');
                if ($phone !== null) {
                    $phone = is_string($phone) ? $phone : '';
                    $user->setPhone($phone);
                }
                $entityManager->persist($user);
                $entityManager->flush();
            }   
        


        return $this->redirectToRoute('profile');
    }

    #[Route('/changePassword', name: 'changePassword',methods: ['POST'])]
    public function ChangeAdminPassword(Request $request,UserPasswordHasherInterface $userPasswordHasher,  EntityManagerInterface $entityManager, AdministrateurRepository $administrateurRepository): Response
    {
       
            $token = $this->tokenStorage->getToken();
            $currentUser = $token->getUser();
            
            if ($currentUser instanceof Administrateur) {
                $dateAjout = $currentUser->getDateAjout()->format('Y-m-d');
                $id = $currentUser->getId();
                $user =$administrateurRepository->findOneBy(['id' => $id]);
                $password = $request->get('password');
                $cpassword= $request->get('renewPassword');
                if ($password==$cpassword){
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $password
                        )
                    );
                    $entityManager->persist($user);
                    $entityManager->flush();
                    return $this->redirectToRoute('profile');
                }
                else{
                    return $this->redirectToRoute('profile', ['error' => 1]);
                }
            }   
        


        return $this->redirectToRoute('profile');
    }

    #[Route('/notif', name: 'notif')]
    public function Notifications(CoursierRepository $coursierRepository): Response
    {
        // return $this->render('administrator/coursier.html.twig', [
        //     'coursiers' => $coursierRepository->findAll(),
        // ]);
        return $this->render('adminv2/coursier.html.twig', [
            'coursiers' => $coursierRepository->findAll(),
        ]);
    }

    #[Route('/tarcking/couriers', name: 'trackCoursier')]
    public function trackCoursier(): Response
    {
        
        return $this->render('adminv2/suivieTours.html.twig');
    }


    #[Route('/perdus', name: 'perdus', methods: ['get'])]
    public function perdus(LivraisonRepository $livraisonRepository,StatutLivraisonRepository $statut ,ManagerRegistry $registry,ClientRepository $clientRepository,EntityManagerInterface $entityManager): Response
    {         $now = new \DateTimeImmutable();

        $livraisons = $statut->findBy(['status_title' => ['echec', 'annuler','non recu']]);
        $resultArray=[];
        foreach ($livraisons as $livraison) {
            $statusDateModifier = $livraison->getStatusDateModifier();
            $diff = $now->diff($statusDateModifier);
            $hoursDifference = $diff->h + $diff->days * 24;
            $disp= $livraison->getDisponibilité();
            if ($hoursDifference > 24 && $disp==0 ) { 
                $resultArray[] = $livraison->getLivraison();
            }

        }
       
        return $this->render('adminv2/perdus.html.twig', [
            'perdus' =>  $resultArray 
        ]);

         }


         #[Route('/trouvé/{id}', name: 'trouvé', methods: ['get'])]
         public function touvé(Request $request,LivraisonRepository $livraisonRepository,StatutLivraisonRepository $statut ,ManagerRegistry $registry,ClientRepository $clientRepository,EntityManagerInterface $entityManager): Response
         {
            $now = new \DateTimeImmutable('now', new \DateTimeZone('Africa/Tunis'));
            $livraisonId = $request->get('id');
         
            $livstat= $entityManager->getRepository(StatutLivraison::class)->findOneBy(['livraison' => $livraisonId]);
            $livstat->setDisponibilité(1);
            $livstat-> setStatusTitle('en attente');
            $livstat-> setStatusDateModifier($now);

            $entityManager->flush();
            return $this->redirectToRoute('perdus');
     
     
              }




              #[Route('/suivreCoursier', name: 'suivreCoursier', methods: ['GET'])]
              public function suivreCoursier(LivraisonRepository $livraisonRepository,AdresseRepository $adresseRepository,
              StatutCoursierRepository $statutCoursierRepository,
              StatutLivraisonRepository $statutLivraisonRepository,CoursierRepository $coursierRepository,TournerRepository $tournerRepository): Response
              {    $coursiers=$coursierRepository->findAll();
                   
                $tournées = [];
               
                foreach ($coursiers as $item) {
                    $tour=$tournerRepository->findBy(['coursier' => $item->getId(),'statut_tourner'=>'terminé']);
                    
                    $tournées[] = $tour;
                   
                }



                  return $this->render('adminv2/suivreCoursiers.html.twig', [
                      'coursiers' => $coursiers, 
                      'tournées' => $tournées,
                     
                  ]);
              }



              #[Route('/pathhistory/{id}', name: 'pathhistory', methods: ['GET'])]
              public function pathhistory(Request $request,CoursierPositionHistoryRepository $CoursierPositionHistoryRepository,AdresseRepository $adresseRepository,
              StatutCoursierRepository $statutCoursierRepository,EntityManagerInterface $entityManager,
              StatutLivraisonRepository $statutLivraisonRepository,CoursierRepository $coursierRepository,TournerRepository $tournerRepository,LivraisonRepository $LivraisonRepository): Response
              {    
                $id = $request->get('id');
                $tour=$CoursierPositionHistoryRepository->findBy(['tourner' => $id ]);
                   
                $livs=$LivraisonRepository->findBy(['tourner' => $id ]);
                $adresses = [];
                foreach ($livs as $item) {
                    $ad= $entityManager->getRepository(Adresse::class)->findOneBy(['id' => $item->getAddressId()]);
                    
                    $adresses[] = $ad;
                   
                }
                return $this->render('adminv2/coursierpath.html.twig', [
                      'tour' => $tour, 
                      'adresses'=> $adresses,
                            ]);


                   }

              

}


