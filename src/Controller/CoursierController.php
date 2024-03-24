<?php

namespace App\Controller;

use App\Entity\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Entity\Coursier;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\LivraisonRepository;
use App\Repository\StatutLivraisonRepository;
use App\Repository\CoursierRepository;
use App\Repository\TournerRepository;
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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;
use DateTime; 
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
    public function afficher_tourneesAU(EntityManagerInterface $entityManager,TournerRepository $tournerRepository,LivraisonRepository $livraisonRepository): Response
    {
  $error=0;
    $token = $this->tokenStorage->getToken();
            $currentUser = $token->getUser();
            if ($currentUser instanceof Coursier) {
                $idcoursier = $currentUser->getId();
            }

    $now = new \DateTimeImmutable();
    $tourneé = $entityManager->getRepository(Tourner::class)->findOneBy(['coursier' =>  $idcoursier,'statut_tourner'=>'en cours','date' => $now]);
  
  
    if($tourneé){
        $livraisons= $livraisonRepository->findBy(['tourner' => $tourneé->getId()]);
        return $this->render('coursierV2/home.html.twig', [
            'livraisons' =>  $livraisons,    'date' =>  $now ,'error' =>  $error
        ]);
        
    }else{
        $error=1;
    return $this->render('coursierV2/home.html.twig', [
              'date' =>  $now , 'error' =>  $error
        ]);}
        
    }











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
            $error=2;

            $livstat->setStatusTitle('confirmé');
        } elseif ($submitButton === 'pin') {
          if($pinform== $pinlivraison){
            $error=2;
            $livstat->setStatusTitle('confirmé');

            $livstat->setNote( 'confirmé avec code pin ');
          }else{
            $error=1;
            return $this->render('coursier/confrm.html.twig', [
                'livraisonId' =>  $livraisonId ,'error' =>  $error
            ]);
          }

           
        }


            $entityManager->flush();
            return $this->redirectToRoute('afficher_tourneesAU');
    }


    #[Route('/echecsubmit', name: 'echecsubmit', methods: ['post'])]
    public function echecsubmit(Request $request ,StatutLivraisonRepository $statutLivraison,EntityManagerInterface $entityManager,AdresseRepository $adresseRepository): Response
    {   $newraison=$request->get('newItem');
        $livraisonId = $request->get('livraisonId');
        $livraison= $entityManager->getRepository(Livraison::class)->findOneBy(['id' =>  $livraisonId]);
        $livstat= $entityManager->getRepository(StatutLivraison::class)->findOneBy(['livraison' => $livraisonId]);
        if ($livstat) {
       
            $livstat->setStatusTitle('echec');


            if ($newraison=='') {
            $livstat->setNote($request->request->get('selectedItem'));}
            else{
           $livstat->setNote( $newraison);
            }
            $livraison->setTourner(null);
        }


            $entityManager->flush();
            return $this->redirectToRoute('afficher_tourneesAU');
    }

    #[Route('/coursier_annulation/{id}', name: 'coursier_annulation', methods: ['GET'])]
    public function coursier_annulation(Request $request,RaisonsEchecRepository $raisonsEchecRepository ,LivraisonRepository $livraisonRepository,EntityManagerInterface $entityManager,AdresseRepository $adresseRepository): Response
    {
        $livraisonId = $request->get('id');
        $livraison= $entityManager->getRepository(Livraison::class)->findOneBy(['id' =>  $livraisonId]);
        $livstat= $entityManager->getRepository(StatutLivraison::class)->findOneBy(['livraison' => $livraisonId]);
        $livstat->setStatusTitle('annulée');
      
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
        return $this->redirectToRoute('afficher_tournees');
    }










    #[Route('/disponibilté', name: 'disponibilté', methods: ['GET'])]
    public function disponibilté(Request $request,StatutCoursierRepository $statutCoursierRepository): Response
    {      $token = $this->tokenStorage->getToken();
        $currentUser = $token->getUser();
        if ($currentUser instanceof Coursier) {
            $idcoursier = $currentUser->getId();
        }
       
        $status = $statutCoursierRepository->findBy(['coursier' =>  $idcoursier,'titre_statut' => 'disponible']);
        $error = $request->query->get('error', 0);
       
        return $this->render('coursierV2/disponibilté.html.twig', [
            'status' =>   $status,'error'=>   $error
        ]);
    }


    #[Route('/ajout_disponibilté', name: 'ajout_disponibilté', methods: ['post'])]
    public function ajout_disponibilté(StatutCoursierRepository $statutCoursierRepository,Request $request, EntityManagerInterface $entityManager,SessionInterface $session ): Response
    {  
        $token = $this->tokenStorage->getToken();
        $currentUser = $token->getUser();
        if ($currentUser instanceof Coursier) {
            $idcoursier = $currentUser->getId();
        }
        $error=0;
        $region = $request->get('region');
        $date = $request->get('date');
        $datedate = DateTime::createFromFormat('Y-m-d', $date);
        $currentDate = new DateTime();

        $existingStatut = $entityManager->getRepository(StatutCoursier::class)->findOneBy(['debut_tourner' => $datedate,'coursier'=> $idcoursier]);
        if($datedate <= $currentDate){
            $error=2;
            $livraisonId = $request->get('id');
            $status = $statutCoursierRepository->findBy(['coursier' => $idcoursier,'titre_statut' => 'disponible']);
           
           
            return $this->render('coursierV2/disponibilté.html.twig', [
                'status' =>   $status,'error'=>   $error
            ]);

        }else{
        if (!$existingStatut) {
        $statut = new StatutCoursier();  
        $statut->setDebutTourner($datedate);
        $statut->setRegion($region);
        $statut->setTitreStatut('disponible');
        $cour= $entityManager->getRepository(Coursier::class)->findOneBy(['id' => $idcoursier]);
        $statut->setCoursier($cour);
        $error=4 ;
        $entityManager->persist( $statut);
        $entityManager->flush();
        return $this->redirectToRoute('disponibilté',['error' => $error]);

        }else{
            $error=1;
            $livraisonId = $request->get('id');
            $status = $statutCoursierRepository->findBy(['coursier' => $idcoursier,'titre_statut' => 'disponible']);
           
           
            return $this->render('coursierV2/disponibilté.html.twig', [
                'status' =>   $status,'error'=>   $error
            ]);
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
        public function profile(): Response
        {
            $token = $this->tokenStorage->getToken();
            $currentUser = $token->getUser();
            if ($currentUser instanceof Coursier) {
                $dateAjout = $currentUser->getDateAjout()->format('Y-m-d');
            }

            return $this->render('coursierV2/profile.html.twig', [
                'user' =>  $currentUser,
                'dateAjout' =>  $dateAjout,
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
  
}
