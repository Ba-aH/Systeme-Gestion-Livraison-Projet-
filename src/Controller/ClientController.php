<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Entity\Colis;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Entity\Coursier;
use App\Entity\Client;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\LivraisonRepository;
use App\Repository\StatutLivraisonRepository;
use App\Repository\CoursierRepository;
use App\Repository\TournerRepository;
use App\Repository\ColisRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\LivraisonHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AdresseRepository ;
use App\Repository\StatutCoursierRepository;

use App\Repository\RaisonsEchecRepository;
use App\Entity\Tourner;
use App\Entity\StatutCoursier;
use App\Entity\StatutLivraison;
use App\Entity\Livraison;
use App\Entity\Region;
use DateTime; 
use App\Repository\ClientRepository;
use App\Repository\RegionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/client')]
class ClientController extends AbstractController
{

    private $tokenStorage;
    private $serializer;
    

    public function __construct(TokenStorageInterface $tokenStorage,SerializerInterface $serializer,StatutCoursierRepository $statutCoursierRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->serializer = $serializer;
        
    }
    
    #[Route('/mes-livraisons', name: 'afficher_livraisons', methods: ['GET'])]
    public function afficher_livraisons(Request $request ,LivraisonHistoryRepository $livraisonHistoryRepository,EntityManagerInterface $entityManager,TournerRepository $tournerRepository,LivraisonRepository $livraisonRepository): Response
    {     
        $token = $this->tokenStorage->getToken();
        $currentUser = $token->getUser();
        if ($currentUser instanceof Client) {
            $identifier = $currentUser->getUserIdentifier();
            $id = $currentUser->getId();
            $username = $currentUser->getUsername();
            $nom = $currentUser->getNom();
            $prenom = $currentUser->getPrenom();
            $phone = $currentUser->getPhone();
        }

        $livraisonshistory = $livraisonHistoryRepository->findAll();
        $livhistorytab= [];
     
        foreach ($livraisonshistory as $item) {
          $currentliv =$item->getLivraison();
          $cli=$currentliv->getClient();
            if($cli->getId()==$id){
    
                $livhistorytab[]=$currentliv;
            }
         }

    $tablivraison=$livraisonRepository->findBy(['client' => $id]);
    $livraisons=[];
    foreach ($tablivraison as $item) {
       $id=$item->getId();
       $liv = $entityManager->getRepository(StatutLivraison::class)->findOneBy(['livraison' => $id]);
       if ($liv->getStatusTitle() != 'confirmé' && $liv->getStatusTitle() != 'annulée par client')   {
        $livraisons[]=$item;
         }

    }
         $error = $request->query->get('error', 0);
        return $this->render('client/viw.html.twig', [
            'livraisons' =>  $livraisons ,'error'=> $error, 'livraisonshistory' =>   $livhistorytab 
        ]);
        
    }

 
    #[Route('/client_annulation/{id}', name: 'client_annulation', methods: ['GET'])]
    public function  client_annulation(Request $request,RaisonsEchecRepository $raisonsEchecRepository ,LivraisonRepository $livraisonRepository,EntityManagerInterface $entityManager,AdresseRepository $adresseRepository): Response
    {   $error=0;
        $livraisonId = $request->get('id');
      
        $livraison= $entityManager->getRepository(Livraison::class)->findOneBy(['id' =>  $livraisonId]);
        $livstat= $entityManager->getRepository(StatutLivraison::class)->findOneBy(['livraison' => $livraisonId]);
        $stat= $livstat->getStatusTitle();
      if( $stat=='en cours'){
      
        $error=1;
        return $this->redirectToRoute('afficher_livraisons', ['error' => 1]);

      }else{
          if($livstat->getStatusTitle()=='affecte'){
            $prix= $livraison->getPrixTotaleLivraison();
            $poid = $livraison->getPoidLivraison();
            $tour=$livraison->getTourner();
            $nb=$tour->getNbLivraison();
            $poidTour =  $tour->getPoidTourner() - $poid ;
            $prixTour =$tour->getPrixTourner() -  $prix ;
            $tour -> setPoidTourner($poidTour);
            $tour -> setPrixTourner($prixTour);
            $tour -> setNbLivraison($nb-1);
            $livstat->setStatusTitle('annulée par client');
            $livraison->setTourner(null);
   
            $entityManager->flush();
            return $this->redirectToRoute('afficher_livraisons', ['error' => 2]);
           
          }else{

        $livstat->setStatusTitle('annulée par client');
        $livraison->setTourner(null);
        
       
        $entityManager->flush();
        return $this->redirectToRoute('afficher_livraisons', ['error' => 2]);}

      }
        
       
    }
   

    #[Route('/editbyclient/{id}', name: 'editbyclient', methods: ['GET'])]
    public function editbyclient(Request $request ,LivraisonRepository $livraisonRepository,EntityManagerInterface $entityManager,AdresseRepository $adresseRepository): Response
    {
        $token = $this->tokenStorage->getToken();
        $currentUser = $token->getUser();
        if ($currentUser instanceof Client) {
            $identifier = $currentUser->getUserIdentifier();
            $id = $currentUser->getId();
        }
        $livraisonId = $request->get('id');
        $liv = $entityManager->getRepository(Livraison::class)->findOneBy(['id' =>  $livraisonId]);
        $address=$adresseRepository->findOneBy(['id' => $liv->getAddressId()]);
       $items=$adresseRepository->findBy(['client' => $id]);
       $error = $request->query->get('error', 0);
        return $this->render('client/confirm.html.twig', [
            'liv' =>  $liv,'address' =>$address , 'items'=>$items,'error'=>$error
        ]);
    }
    #[Route('/consultclient/{id}', name: 'consultclient', methods: ['GET'])]
    public function consultclient(Request $request ,LivraisonRepository $livraisonRepository,EntityManagerInterface $entityManager,AdresseRepository $adresseRepository): Response
    {
        $token = $this->tokenStorage->getToken();
        $currentUser = $token->getUser();
        if ($currentUser instanceof Client) {
            $identifier = $currentUser->getUserIdentifier();
            $id = $currentUser->getId();
        }
        $livraisonId = $request->get('id');
        $liv = $entityManager->getRepository(Livraison::class)->findOneBy(['id' =>  $livraisonId]);
        $address=$adresseRepository->findOneBy(['id' => $liv->getAddressId()]);
        $statut=$entityManager->getRepository(StatutLivraison::class)->findOneBy(['livraison' =>  $livraisonId]);
        $error = $request->query->get('error', 0);
        return $this->render('client/consult.html.twig', [
            'liv' =>  $liv,'address' =>$address ,'error'=>$error,'stat'=>$statut
        ]);
    }




    #[Route('/liv_non_recu/{id}', name: 'liv_non_recu', methods: ['GET'])]
    public function liv_non_recu(Request $request ,LivraisonRepository $livraisonRepository,EntityManagerInterface $entityManager): Response
    {
        $token = $this->tokenStorage->getToken();
        $currentUser = $token->getUser();
        if ($currentUser instanceof Client) {
            $identifier = $currentUser->getUserIdentifier();
            $id = $currentUser->getId();
        }
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        $livraisonId = $request->get('id');
        $livstat= $entityManager->getRepository(StatutLivraison::class)->findOneBy(['livraison' => $livraisonId]);
        $livstat->setStatusTitle('non recu');
        $livstat->setStatusDateModifier($now);
        $entityManager->flush();
        return $this->redirectToRoute('afficher_livraisons');}
    



    #[Route('/confirmedit', name: 'confirmedit', methods: ['post'])]
    public function confirmedit(Request $request ,StatutLivraisonRepository $statutLivraison,EntityManagerInterface $entityManager,AdresseRepository $adresseRepository): Response
    {   $reference = $request->request->get('reference');
        $idd = $request->request->get('idd');
        $address = $request->request->get('items');
        $error=0;
        $currentDate = new DateTime();
        // Convert the string to a DateTime object
        $dateLivraison = DateTime::createFromFormat('Y-m-d', $reference);
        $livraison= $entityManager->getRepository(Livraison::class)->findOneBy(['id' =>  $idd]);

        $livstat= $entityManager->getRepository(StatutLivraison::class)->findOneBy(['livraison' => $idd]);
        $stat=  $livstat->getStatusTitle();

        if($dateLivraison <= $currentDate){

            $error=2;
            return $this->redirectToRoute('editbyclient', ['id' => $idd, 'error' => $error]);


        }

        else{


        if ($stat != 'en cours') {
            $error=3;
            $livraison->setAddressId($address);
            $livraison->setLivraisonDate($dateLivraison);
            $livstat->setStatusTitle('en attente');
            $livraison->setTourner(null);

            $entityManager->flush();
            return $this->redirectToRoute('editbyclient', ['id' => $idd, 'error' => $error]);}
            else{

                $error=1;
                return $this->redirectToRoute('editbyclient', ['id' => $idd, 'error' => $error]);


            }

    }
}




#[Route('/historyclient', name: 'historyclient')]
public function history(Request $request,EntityManagerInterface $entityManager,LivraisonHistoryRepository $livraisonHistoryRepository,LivraisonRepository $livraisonRepository,AdresseRepository $adresseRepository,ClientRepository $clientRepository,CoursierRepository $coursierRepository): Response
{   
    $token = $this->tokenStorage->getToken();
    $currentUser = $token->getUser();
    if ($currentUser instanceof Client) {
        $identifier = $currentUser->getUserIdentifier();
        $id = $currentUser->getId();
        $username = $currentUser->getUsername();
        $nom = $currentUser->getNom();
        $prenom = $currentUser->getPrenom();
        $phone = $currentUser->getPhone();
    }
    $livraisonshistory = $livraisonHistoryRepository->findAll();
    $livhistorytab= [];
 
    foreach ($livraisonshistory as $item) {
      $currentliv =$item->getLivraison();
      $cli=$currentliv->getClient();
        if($cli->getId()==$id){

            $livhistorytab[]=$currentliv;
        }
     }
     return $this->render('client/hist.html.twig', [
        'livraisons' =>   $livhistorytab 
    ]); }
    

     
    #[Route('/history_details/{id}', name: 'history_details', methods: ['GET'])]
    public function history_details(Request $request ,ColisRepository $colisRepository,LivraisonRepository $livraisonRepository,EntityManagerInterface $entityManager,AdresseRepository $adresseRepository): Response
    {
        $livraisonId = $request->get('id');
        $liv = $entityManager->getRepository(Livraison::class)->findOneBy(['id' =>  $livraisonId]);
        $address=$adresseRepository->findOneBy(['id' => $liv->getAddressId()]);
        $colis=$colisRepository->findBy(['livraison' =>$livraisonId ]);
        $error = $request->query->get('error', 0);
        return $this->render('client/details.html.twig', [
            'liv' =>  $liv,'address' =>$address ,'error'=>$error, 'colis' =>$colis
        ]);
    }


      
    #[Route('/nonrecu/{id}', name: 'nonrecu', methods: ['GET'])]
    public function colis_nonrecu(Request $request ,ColisRepository $colisRepository,LivraisonRepository $livraisonRepository,EntityManagerInterface $entityManager,AdresseRepository $adresseRepository): Response
    {   $idd = $request->get('idliv');        
        $colisId = $request->get('id');
        $coli = $entityManager->getRepository(Colis::class)->findOneBy(['id' =>  $colisId]);
        $coli->setStatut('non recu');
        $entityManager->flush();
        return $this->redirectToRoute('history_details', ['id' => $idd]);

    }

    #[Route('/profile', name: 'clientProfile')]
        public function profile(Request $request,AdresseRepository $adresseRepository,RegionRepository $RegionRepository): Response
        {
            $token = $this->tokenStorage->getToken();
            $currentUser = $token->getUser();
            if ($currentUser instanceof Client) {
                $dateAjout = $currentUser->getDateAjout()->format('Y-m-d');
                $adresses=$adresseRepository->findBy(['client'=>$currentUser->getId()]);
            }
            $regions=$RegionRepository->findAll();
            $error = $request->query->get('error', 0);
            return $this->render('client/profile.html.twig', [
                'user' =>  $currentUser,
                'dateAjout' =>  $dateAjout,
                'adresses' => $adresses,
                'error'=> $error,
                'regions' => $regions
            ]);
        }

        #[Route('/update-profile', name: 'update_client_profile')]
        public function updateProfile(Request $request, EntityManagerInterface $entityManager, ClientRepository $clientRepository): Response
        {
            $token = $this->tokenStorage->getToken();
            $currentUser = $token->getUser();
            
            if ($currentUser instanceof Client) {
                $dateAjout = $currentUser->getDateAjout()->format('Y-m-d');
                $id = $currentUser->getId();
                $user =$clientRepository->findOneBy(['id' => $id]);
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
            
            
            return $this->redirectToRoute('clientProfile');
        }

        #[Route('/ajout-adresse', name: 'ajout-adresse')]
        public function ajoutAdr(Request $request, EntityManagerInterface $entityManager, AdresseRepository $adresseRepository): Response
        {
            $token = $this->tokenStorage->getToken();
            $currentUser = $token->getUser();
            $address = $request->request->get('address');
            $ville = $request->request->get('ville');
            $region = $request->request->get('region');
            $zip = $request->request->get('zipcode');
            $reg = $entityManager->getRepository(Region::class)->findOneBy(['id' =>  $region]);
            if (!empty($address)) {
                $adr = new Adresse();
                $adr->setVille($ville);
                $adr->setRegion($reg);
                $adr->setFormattedAddress($address);
                $adr->setZipCode($zip);
                if ($currentUser instanceof Client) {
                    $adr->setClient($currentUser);
                    $entityManager->persist($adr);
                    $entityManager->flush();
                }
            }
            return $this->redirectToRoute('clientProfile');
        }

        #[Route('/delete-adresse', name: 'delete-adresse')]
        public function deleteAdr(Request $request, EntityManagerInterface $entityManager, AdresseRepository $adresseRepository): Response
        {
            $token = $this->tokenStorage->getToken();
            $currentUser = $token->getUser();
            $id = $request->get('id');
            $adr=$adresseRepository->findOneBy(['id'=>$id]);
            $entityManager->remove($adr);
            $entityManager->flush();
 
            return $this->redirectToRoute('clientProfile');
        }

        #[Route('/changePassword_client', name: 'changePassword_client',methods: ['POST'])]
        public function ChangeAdminPassword(Request $request,UserPasswordHasherInterface $userPasswordHasher,  EntityManagerInterface $entityManager, ClientRepository $clientRepository): Response
        {
           
                $token = $this->tokenStorage->getToken();
                $currentUser = $token->getUser();
                
                if ($currentUser instanceof Client) {
                    $dateAjout = $currentUser->getDateAjout()->format('Y-m-d');
                    $id = $currentUser->getId();
                    $user =$clientRepository->findOneBy(['id' => $id]);
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
                        return $this->redirectToRoute('clientProfile');
                    }
                    else{
                        return $this->redirectToRoute('clientProfile', ['error' => 1]);
                    }
                }   
            
    
    
            return $this->redirectToRoute('clientProfile');
        }

        #[Route('', name: 'ClinetIndex')]
        public function index(): Response
        {      
            $token = $this->tokenStorage->getToken();
            $currentUser = $token->getUser();
            return $this->render('client/index.html.twig', [
                'user' =>  $currentUser
            ]);     
        }

        #[Route('/clientheader', name: 'clientheader')]
        public function clientheader(): Response
        {      
            $token = $this->tokenStorage->getToken();
            $currentUser = $token->getUser();
            return $this->render('clientHeader.html.twig', [
                'user' =>  $currentUser
            ]);     
        }
}
