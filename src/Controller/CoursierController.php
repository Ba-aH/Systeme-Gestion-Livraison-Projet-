<?php

namespace App\Controller;

use App\Entity\Adresse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Client;
use App\Entity\RaisonsEchec;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Entity\Coursier;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\LivraisonRepository;
use App\Repository\StatutLivraisonRepository;
use App\Repository\CoursierRepository;
use App\Repository\TournerRepository;
use App\Repository\RegionRepository;
use App\Repository\ClientRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AdresseRepository ;
use App\Repository\StatutCoursierRepository;
use App\Repository\RaisonsEchecRepository;
use App\Entity\Tourner;
use App\Entity\StatutCoursier;
use App\Entity\StatutLivraison;
use App\Entity\Livraison;
use App\Entity\Region;
use App\Entity\Warehouse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;
use DateTime; 
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
#[Route('/coursier')]
class CoursierController extends AbstractController
{
    private $tokenStorage;
    private $serializer;

    public function __construct(TokenStorageInterface $tokenStorage,SerializerInterface $serializer,StatutCoursierRepository $statutCoursierRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->serializer = $serializer;
        
    }
    #[Route('/test', name: 'test', methods: ['GET'])]
    public function index(LivraisonRepository $livraisonRepository): Response
    {
        return $this->render('coursier/disponibilité.html.twig');
    }
    #[Route('/details/{id}', name: 'details', methods: ['GET'])]
    public function details(Request $request ,LivraisonRepository $livraisonRepository,EntityManagerInterface $entityManager,AdresseRepository $adresseRepository): Response
    {
        $livraisonId = $request->get('id');
        $liv = $entityManager->getRepository(Livraison::class)->findOneBy(['id' =>  $livraisonId]);
        $client=$liv->getClient();
        $address=$adresseRepository->findOneBy(['id' => $liv->getAddressId()]);
        $latitude=$address->getLatitude();
        $longtitude=$address->getLongitude();
        // return $this->render('coursier/details.html.twig', [
        //     'liv' =>  $liv,'client' =>$client,'address' =>$address,'lat'=>$latitude, 'long'=>$longtitude,
        // ]);

        return $this->render('coursierV2/details.html.twig', [
            'liv' =>  $liv,'client' =>$client,'address' =>$address,'lat'=>$latitude, 'long'=>$longtitude,
        ]);
    }


    #[Route('/afficher_tournees', name: 'afficher_tournees', methods: ['GET'])]
    public function afficher_tournees(EntityManagerInterface $entityManager,TournerRepository $tournerRepository,LivraisonRepository $livraisonRepository): Response
    {
    $idcoursier=9;
    $tabtourner=$tournerRepository->findBy(['coursier' => 9,'statut_tourner'=>'en cours']);
    


    $livraisons=[];
    foreach ($tabtourner as $item) {
        $livraisons[] = $livraisonRepository->findBy(['tourner' => $item->getId()]);
    }

    $status=[];
    foreach ($livraisons as $livraisonTable) {
        // Iterate over the items inside each table
        foreach ($livraisonTable as $livraison) {
            $livraisonId = $livraison->getId();
            $livstat= $entityManager->getRepository(StatutLivraison::class)->findOneBy(['livraison' => $livraisonId]);
            $status[]=  $livstat->getStatusTitle();
            // Access more properties as needed
        }
    }


        return $this->render('coursier/temp.html.twig', [
            'livraisons' =>  $livraisons,   'status' =>  $status
        ]);
        
    }
       





    #[Route('/afficher_tourneesAU', name: 'afficher_tourneesAU', methods: ['GET'])]
    public function afficher_tourneesAU(EntityManagerInterface $entityManager,Request $request,TournerRepository $tournerRepository,LivraisonRepository $livraisonRepository): Response
    {
        $start = $request->query->get('start', 0);

    $error=0;
    $token = $this->tokenStorage->getToken();
            $currentUser = $token->getUser();
            if ($currentUser instanceof Coursier) {
                $idcoursier = $currentUser->getId();
            }

    $now = new \DateTimeImmutable();
    $tourneé = $entityManager->getRepository(Tourner::class)->findOneBy(['coursier' =>  $idcoursier,'statut_tourner'=>['a faire', 'complet','en cours'],'date' => $now]);
  
  
    if($tourneé){
        $id=$tourneé->getId();
        $livraisons= $livraisonRepository->findBy(['tourner' => $tourneé->getId()]);
       $idregion=0;
        $status=[];
        foreach ($livraisons as $item) {
       
            $livraisonId = $item->getId();
            $livstat= $entityManager->getRepository(StatutLivraison::class)->findOneBy(['livraison' => $livraisonId]);
            $status[]=  $livstat->getStatusTitle();
            
        }

        
        $adresse=[];
        foreach ($livraisons as $item) {

            $livraisonad = $item->getAddressId();
            $ad= $entityManager->getRepository(Adresse::class)->findOneBy(['id' => $livraisonad]);
            $idregion=$ad->getRegion();
            $adresse[]=  $ad;
            
        }

        $warehouse= $entityManager->getRepository(Warehouse::class)->findOneBy(['region' => $idregion]);

       return $this->render('coursierV2/home.html.twig', [
            'livraisons' =>  $livraisons,
            'tour' =>  $tourneé->getId(),
            'date' =>  $now ,
            'error' =>  $error,'idtour'=>  $id,'start'=>$start ,'status'=>$status,'address'=>$adresse,'warehouse'=>$warehouse
            ,'distance'=>null
        ]);
        
    }else{ 
        $error=1;
    return $this->render('coursierV2/home.html.twig', [
              'date' =>  $now , 'error' =>  $error ,'idtour'=>null , 'livraisons' => null ,'start'=>$start,'status'=>0,'address'=>null
              ,'warehouse'=>null,'distance'=>null
        ]);}
        
    }

    #[Route('/afficher_tourneesrecent', name: 'afficher_tourneesrecent', methods: ['GET'])]
    public function afficher_tourneesrecent(EntityManagerInterface $entityManager,TournerRepository $tournerRepository,LivraisonRepository $livraisonRepository): Response
    {
    $error=0;
    $token = $this->tokenStorage->getToken();
            $currentUser = $token->getUser();
            if ($currentUser instanceof Coursier) {
                $idcoursier = $currentUser->getId();
            }
            $now = new \DateTimeImmutable();


    $tourneés = $tournerRepository->findBy(['coursier' =>  $idcoursier,'statut_tourner' => 'términé']);
    $livraisons=[];
    if($tourneés){
     foreach ($tourneés as $item) {
   
        $livraisons[]= $livraisonRepository->findBy(['tourner' => $item->getId()]);
    }
    
        
  
        return $this->render('coursierV2/recent.html.twig', [
            'livraisons' =>  $livraisons, 'error' =>  $error,]);}
        else{
            $error=1;
            return $this->render('coursierV2/recent.html.twig', [
                'livraisons' =>  $livraisons, 'error' =>  $error,]);}}
        
        
    


                #[Route('/afaire', name: 'afaire', methods: ['GET'])]
                public function afaire(EntityManagerInterface $entityManager,TournerRepository $tournerRepository,LivraisonRepository $livraisonRepository): Response
                {
                $error=0;
                $token = $this->tokenStorage->getToken();
                        $currentUser = $token->getUser();
                        if ($currentUser instanceof Coursier) {
                            $idcoursier = $currentUser->getId();
                        }
                     
            
                $tourneés = $tournerRepository->findBy(['coursier' =>  $idcoursier,'statut_tourner' => 'a faire']);
                $livraisons=[];
                if($tourneés){
                 foreach ($tourneés as $item) {
               
                    $livraisons[]= $livraisonRepository->findBy(['tourner' => $item->getId()]);
                }
                
                    
              
                    return $this->render('coursierV2/futur_tour.html.twig', [
                        'livraisons' =>  $livraisons, 'error' =>  $error,]);}
                    else{
                        $error=1;
                        return $this->render('coursierV2/futur_tour.html.twig', [
                            'livraisons' =>  $livraisons, 'error' =>  $error,]);}}
                    
                    
                
            






    #[Route('/echecc/{id}', name: 'echecc', methods: ['GET'])]
    public function echecc(Request $request,RaisonsEchecRepository $raisonsEchecRepository ,LivraisonRepository $livraisonRepository,EntityManagerInterface $entityManager,AdresseRepository $adresseRepository): Response
    {
        $livraisonId = $request->get('id');

        $listraisons=$raisonsEchecRepository->findAll();
       
        return $this->render('coursierV2/echec.html.twig', [
            'livraisonId' =>  $livraisonId,  'list' =>  $listraisons,
        ]);
    }
    #[Route('/confirmer/{id}', name: 'confirmer', methods: ['GET'])]
    public function confirmer(Request $request,RaisonsEchecRepository $raisonsEchecRepository ,LivraisonRepository $livraisonRepository,EntityManagerInterface $entityManager,AdresseRepository $adresseRepository): Response
    {
        $livraisonId = $request->get('id');
       $error=0;
       
       
        return $this->render('coursierV2/confirm.html.twig', [
            'livraisonId' =>  $livraisonId,'error' =>  $error
        ]);
    }
    #[Route('/confirmsubmit', name: 'confirmsubmit', methods: ['post'])]
    public function confirmsubmit(Request $request ,StatutLivraisonRepository $statutLivraison,EntityManagerInterface $entityManager,AdresseRepository $adresseRepository): Response
    {   $pinform = $request->get('pin');
        $error=0;
        $livraisonId = $request->get('livraisonId');
        $livraison= $entityManager->getRepository(Livraison::class)->findOneBy(['id' =>  $livraisonId]);
        $pinlivraison= $livraison->getCodePin();
        $livstat= $entityManager->getRepository(StatutLivraison::class)->findOneBy(['livraison' => $livraisonId]);
        $submitButton = $request->request->get('submit_button');
        if ($submitButton === 'normal') {
            $now = new \DateTimeImmutable('now', new \DateTimeZone('Africa/Tunis'));
            $error=2;
            $livstat->setStatusTitle('confirmé');
            $livstat->setStatusDateModifier($now);
            
        } elseif ($submitButton === 'pin') {
          if($pinform== $pinlivraison){
            $now = new \DateTimeImmutable('now', new \DateTimeZone('Africa/Tunis'));
            $error=2;
            $livstat->setStatusTitle('confirmé');
            $livstat->setStatusDateModifier($now);
            $livstat->setNote( 'confirmé avec code pin ');
          }else{
            $error=1;
            return $this->render('coursier/confrm.html.twig', [
                'livraisonId' =>  $livraisonId ,'error' =>  $error
            ]);
          }

           
        }


            $entityManager->flush();
            $start=1;
            return $this->redirectToRoute('afficher_tourneesAU',['start' => $start]);
           
    }


    #[Route('/echecsubmit', name: 'echecsubmit', methods: ['post'])]
    public function echecsubmit(Request $request ,StatutLivraisonRepository $statutLivraison,EntityManagerInterface $entityManager,AdresseRepository $adresseRepository): Response
    {   $newraison=$request->get('newItem');
        $livraisonId = $request->get('livraisonId');
        $livraison= $entityManager->getRepository(Livraison::class)->findOneBy(['id' =>  $livraisonId]);
        $livstat= $entityManager->getRepository(StatutLivraison::class)->findOneBy(['livraison' => $livraisonId]);
        $idreason=$request->get('selectedItem');
        $res=$request->get('selectedres');
        
        if ($livstat) {
            $now = new \DateTimeImmutable('now', new \DateTimeZone('Africa/Tunis'));
            $livstat->setStatusTitle('echec');
            $livstat->setStatusDateModifier($now);

            if ($newraison!='') { $reason= new RaisonsEchec();
           $reason->setRaison($newraison);
           $entityManager->persist($reason);
           $livstat->setRaisonEchec($reason);}
               
            else{
           $reason= $entityManager->getRepository(RaisonsEchec::class)->findOneBy(['id' => $idreason]);
            $livstat->setRaisonEchec($reason);}
            
            $livraison->setTourner(null);
        }


            $entityManager->flush();
            $start=1;
            return $this->redirectToRoute('afficher_tourneesAU',['start' => $start]);
    }

    #[Route('/coursier_annulation/{id}', name: 'coursier_annulation', methods: ['GET'])]
    public function coursier_annulation(Request $request,RaisonsEchecRepository $raisonsEchecRepository ,LivraisonRepository $livraisonRepository,EntityManagerInterface $entityManager,AdresseRepository $adresseRepository): Response
    {
        $livraisonId = $request->get('id');
        $livraison= $entityManager->getRepository(Livraison::class)->findOneBy(['id' =>  $livraisonId]);
        $livstat= $entityManager->getRepository(StatutLivraison::class)->findOneBy(['livraison' => $livraisonId]);
        $now = new \DateTimeImmutable('now', new \DateTimeZone('Africa/Tunis'));
        $livstat->setStatusTitle('annulée');
        $livstat->setStatusDateModifier($now);
      
        $prix= $livraison->getPrixTotaleLivraison();
        $poid = $livraison->getPoidLivraison();
        $tour=$livraison->getTourner();
        $nb=$tour->getNbLivraison();
        $poidTour =  $tour->getPoidTourner() - $poid ;
        $prixTour =$tour->getPrixTourner() -  $prix ;
        $tour -> setPoidTourner($poidTour);
        $tour -> setPrixTourner($prixTour);
        $tour -> setNbLivraison($nb-1);
        $livraison->setTourner(null);
        // $livstat->setNote(null);
        $entityManager->flush();
        $start=1;
        return $this->redirectToRoute('afficher_tourneesAU',['start' => $start]);
    }










    #[Route('/disponibilté', name: 'disponibilté', methods: ['GET'])]
    public function disponibilté(RegionRepository $regionRepository,Request $request,StatutCoursierRepository $statutCoursierRepository): Response
    {      $token = $this->tokenStorage->getToken();
        $currentUser = $token->getUser();
        if ($currentUser instanceof Coursier) {
            $idcoursier = $currentUser->getId();
        }
       
        $status = $statutCoursierRepository->findBy(['coursier' =>  $idcoursier,'titre_statut' => 'disponible']);
        $error = $request->query->get('error', 0);
        $regions=$regionRepository->findAll();
        return $this->render('coursierV2/disponibilté.html.twig', [
            'status' =>   $status,'error'=>   $error,'regions'=> $regions
        ]);
    }


    #[Route('/ajout_disponibilté', name: 'ajout_disponibilté', methods: ['post'])]
    public function ajout_disponibilté(RegionRepository $regionRepository,StatutCoursierRepository $statutCoursierRepository,Request $request, EntityManagerInterface $entityManager,SessionInterface $session ): Response
    {  
        $token = $this->tokenStorage->getToken();
        $currentUser = $token->getUser();
        if ($currentUser instanceof Coursier) {
            $idcoursier = $currentUser->getId();
        }
        $error=0;
        $region = $request->get('region');
        $reg= $entityManager->getRepository(Region::class)->findOneBy(['id' =>  $region]);
        $date = $request->get('date');
        $datedate = DateTime::createFromFormat('Y-m-d', $date);
        $currentDate = new DateTime();

        $existingStatut = $entityManager->getRepository(StatutCoursier::class)->findOneBy(['debut_tourner' => $datedate,'coursier'=> $idcoursier]);
        if($datedate <= $currentDate){
            $error=2;
            $livraisonId = $request->get('id');
            $status = $statutCoursierRepository->findBy(['coursier' => $idcoursier,'titre_statut' => 'disponible']);
           
           
            return $this->redirectToRoute('disponibilté',['error' => $error]);

        }else{
        if (!$existingStatut) {
        $statut = new StatutCoursier();  
        $statut->setDebutTourner($datedate);
        $statut->setRegion($reg);
        $statut->setTitreStatut('disponible');
        $cour= $entityManager->getRepository(Coursier::class)->findOneBy(['id' => $idcoursier]);
        $statut->setCoursier($cour);
        $error=4 ;
        $entityManager->persist( $statut);
        $entityManager->flush();
        return $this->redirectToRoute('disponibilté',['error' => $error]);

       
            $livraisonId = $request->get('id');
            $status = $statutCoursierRepository->findBy(['coursier' => $idcoursier,'titre_statut' => 'disponible']);
           
           
            return $this->render('coursierV2/disponibilté.html.twig', [
                'status' =>   $status,'error'=>   $error
            ]);
        }else{
            $error=1;
            return $this->redirectToRoute('disponibilté',['error' => $error]);
        }}
               
        
    }

    #[Route('/annulerstatut/{id}', name: 'annulerstatut', methods: ['POST'])]
    public function annulerstatut(Request $request, StatutCoursier $StatutCoursier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$StatutCoursier->getId(), $request->request->get('_token'))) {
            $entityManager->remove($StatutCoursier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('disponibilté');

    }
    #[Route('/profile', name: 'coursierProfile')]
        public function profile(Request $request): Response
        {
            $token = $this->tokenStorage->getToken();
            $currentUser = $token->getUser();
            if ($currentUser instanceof Coursier) {
                $dateAjout = $currentUser->getDateAjout()->format('Y-m-d');
            }
            $error = $request->query->get('error', 0);
            return $this->render('coursierV2/profile.html.twig', [
                'user' =>  $currentUser,
                'dateAjout' =>  $dateAjout,'error'=> $error
            ]);
        }

        #[Route('/update-profile', name: 'update_coursier_profile')]
        public function updateProfile(Request $request, EntityManagerInterface $entityManager, CoursierRepository $coursierRepository): Response
        {
            $token = $this->tokenStorage->getToken();
            $currentUser = $token->getUser();

            if ($currentUser instanceof Coursier) {
                $dateAjout = $currentUser->getDateAjout()->format('Y-m-d');
                $id = $currentUser->getId();
                $user =$coursierRepository->findOneBy(['id' => $id]);
                $user->setPrenom($request->get('prenom'));
                $user->setUsername($request->get('username'));
                $user->setNom($request->get('nom'));
                $user->setPhone($request->get('phone'));
                $entityManager->persist($user);
                $entityManager->flush();
            }


            return $this->redirectToRoute('coursierProfile');
        }
  


        #[Route('/changePassword_coursier', name: 'changePassword_coursier',methods: ['POST'])]
        public function ChangeAdminPassword(Request $request,UserPasswordHasherInterface $userPasswordHasher,  EntityManagerInterface $entityManager, CoursierRepository $administrateurRepository): Response
        {
           
                $token = $this->tokenStorage->getToken();
                $currentUser = $token->getUser();
                
                if ($currentUser instanceof Coursier) {
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
                        return $this->redirectToRoute('coursierProfile');
                    }
                    else{
                        return $this->redirectToRoute('coursierProfile', ['error' => 1]);
                    }
                }   
            
    
    
            return $this->redirectToRoute('profile');
        }
    


        #[Route('/salarie', name: 'salarie')]
        public function salarie(Request $request,TournerRepository $TournerRepository,EntityManagerInterface $entityManager): Response
        {   $token = $this->tokenStorage->getToken();
            $currentUser = $token->getUser();
            if ($currentUser instanceof Coursier) {
                $idcoursier = $currentUser->getId();
            }
            $coursier= $entityManager->getRepository(Coursier::class)->findOneBy(['id' => $idcoursier]);

        $now = new \DateTimeImmutable();
        $firstDayOfMonth = $now->modify('first day of this month')->setTime(0, 0, 0);
        $lastDayOfMonth = $now->modify('last day of this month')->setTime(23, 59, 59);
        $end = new \DateTimeImmutable();

   $repository = $entityManager->getRepository(Tourner::class);
   $tournées = $repository->createQueryBuilder('t')
    ->where('t.coursier = :idcoursier')
    ->andWhere('t.date BETWEEN :start AND :end')
    ->setParameter('idcoursier', $idcoursier)
    ->setParameter('start', $firstDayOfMonth)
    ->setParameter('end', $end)
    ->getQuery()
    ->getResult();
    $tournéesCount = count($tournées); 

    $tournéesIds = [];
    foreach ($tournées as $tournée) {
        $tournéesIds[] = $tournée->getId(); // Assuming getId() retrieves the ID
    }
    $livraisons = $entityManager->getRepository(Livraison::class)->findBy(['tourner' =>$tournéesIds]);
    // Query Livraison entities based on IDs from $tournées
    $livCount = count($livraisons); 
    $sal=5* $livCount;
     
   $auechec=0;
    $echecauj = $entityManager->getRepository(Livraison::class)->findBy(['livraison_date' =>$now]);
    foreach ($echecauj as $item) {
        if( $item->getTourner()->getCoursier()->getId()== $idcoursier){
       
            $statut = $entityManager->getRepository(StatutLivraison::class)->findOneBy(['livraison' =>$item->getId()]);
            if( $statut->getStatusTitle()=='echec'){
                $auechec++;
            }
        }
    }

    $annulerau=0;
    $annau = $entityManager->getRepository(Livraison::class)->findBy(['livraison_date' =>$now]);
    foreach ($annau as $item) {
        if( $item->getTourner()->getCoursier()->getId()== $idcoursier){
       
            $statut = $entityManager->getRepository(StatutLivraison::class)->findOneBy(['livraison' =>$item->getId()]);
            if( $statut->getStatusTitle()=='annuler'){
                $annulerau++;
            }
        }
    }
    
 $livtoday=0;     
 $livau = $entityManager->getRepository(Livraison::class)->findBy(['livraison_date' =>$now]);
 foreach ($livau as $item) {
     if( $item->getTourner()->getCoursier()->getId()== $idcoursier){
        $livtoday++;
         
     }
 }



 $saltoday=0;
 $sall = $entityManager->getRepository(Livraison::class)->findBy(['livraison_date' =>$now]);
 foreach ($sall as $item) {
     if( $item->getTourner()->getCoursier()->getId()== $idcoursier){
        $statut = $entityManager->getRepository(StatutLivraison::class)->findOneBy(['livraison' =>$item->getId()]);
        if( $statut->getStatusTitle()=='confirmé'){
        $saltoday++;
         }}
 }
$dailysal=$saltoday*5;


            return  $this->render('coursierV2/wallet.html.twig',['tour'=>$tournées,'count'=>$tournéesCount,'livs'=>$livraisons
            ,'livnumber'=>$livCount,'sal'=>$sal ,'coursier'=>$coursier,'echectoday'=>$auechec, 'annulertoday'=>$annulerau,'livtoday'=>$livtoday,'todaysal'=>$dailysal]);



           
        }

        #[Route('/start_tour/{id}', name: 'start_tour', methods: ['GET'])]
        public function start_tour(Request $request ,EntityManagerInterface $entityManager): Response
        {
            $id = $request->get('id');
            $livstat= $entityManager->getRepository(Tourner::class)->findOneBy(['id' => $id]);
            $livstat->setStatutTourner('en cours');
            $entityManager->flush();
            $start=1;
            return $this->redirectToRoute('afficher_tourneesAU',['start' => $start]);
        }
    
        #[Route('/fin_tour/{id}', name: 'fin_tour', methods: ['GET'])]
        public function fin_tour(Request $request ,EntityManagerInterface $entityManager): Response
        {
            $id = $request->get('id');
            $livstat= $entityManager->getRepository(Tourner::class)->findOneBy(['id' => $id]);
            $livstat->setStatutTourner('terminé');
            $entityManager->flush();
            $start=0;
            return $this->redirectToRoute('afficher_tourneesAU',['start' => $start]);
        }
    


        #[Route('/send', name: 'send')]
        public function send(Request $request): Response
        {
            $data = [
                'id' => 1, // Sample ID for demonstration, replace with actual data
                'longitude' => 9.813538, // Sample longitude, replace with actual data
                'latitude' => 36.738884, // Sample latitude, replace with actual data
                'timestamp' => time(), // Include timestamp if needed
            ];
    
            // Return the data as JSON response
            return new JsonResponse($data);
        }


        #[Route('/notificationc', name: 'notificationc', methods: ['GET'])]
        public function notificationc(EntityManagerInterface $entityManager,TournerRepository $tournerRepository,SerializerInterface $serializer): Response
        {
       
        $token = $this->tokenStorage->getToken();
                $currentUser = $token->getUser();
                if ($currentUser instanceof Coursier) {
                    $idcoursier = $currentUser->getId();
                }
        $tourneés = $tournerRepository->findBy(['coursier' =>  $idcoursier,'statut_tourner' => 'complet']);
        $numItems = count($tourneés);

        $data = $serializer->serialize([
            'tournées' => $tourneés,
            'nb' => $numItems,
        ], 'json', [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['coursier','livraison'],
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);

        // Return JsonResponse
        return new JsonResponse($data, 200, [], true);
      
    
        }
}
