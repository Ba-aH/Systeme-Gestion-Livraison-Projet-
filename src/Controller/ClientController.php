<?php

namespace App\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Entity\Coursier;
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
use DateTime; 
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/client')]
class ClientController extends AbstractController
{


    #[Route('/client', name: 'app_client')]
    public function index(): Response
    {
        return $this->render('client/form.html.twig', [
            'controller_name' => 'ClientController',
        ]);
    }










    
    #[Route('/afficher_livraisons', name: 'afficher_livraisons', methods: ['GET'])]
    public function afficher_livraisons(Request $request ,EntityManagerInterface $entityManager,TournerRepository $tournerRepository,LivraisonRepository $livraisonRepository): Response
    {
 
    $tablivraison=$livraisonRepository->findBy(['client' => 12]);
    


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
            'livraisons' =>  $livraisons ,'error'=> $error
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
        $livraisonId = $request->get('id');
        $liv = $entityManager->getRepository(Livraison::class)->findOneBy(['id' =>  $livraisonId]);
        $address=$adresseRepository->findOneBy(['id' => $liv->getAddressId()]);
       $items=$adresseRepository->findBy(['client' => 12]);
       $error = $request->query->get('error', 0);
        return $this->render('client/confirm.html.twig', [
            'liv' =>  $liv,'address' =>$address , 'items'=>$items,'error'=>$error
        ]);
    }
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
    $iduser=12;
    $livraisons = $livraisonHistoryRepository->findAll();
    $livtab= [];
 
    foreach ($livraisons as $item) {
      $currentliv =$item->getLivraison();
      $cli=$currentliv->getClient();
        if($cli->getId()==$iduser){

            $livtab[]=$currentliv;
        }
     }
     return $this->render('client/hist.html.twig', [
        'livraisons' =>   $livtab 
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
}
