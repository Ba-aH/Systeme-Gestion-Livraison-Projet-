<?php

namespace App\Controller;

use App\Entity\Coursier;
use App\Repository\LivraisonRepository;
use App\Repository\StatutLivraisonRepository;
use App\Repository\CoursierRepository;
use App\Repository\AdresseRepository;
use App\Repository\TournerRepository;
use App\Repository\ClientRepository;
use App\Repository\LivraisonHistoryRepository;
use App\Entity\Tourner;
use App\Repository\StatutCoursierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use DateTime; 

#[Route('/administrator')]
class GererTourController extends AbstractController
{
    #[Route('/gerer/tour', name: 'app_gerer_tour', methods: ['GET'])]
    public function index(LivraisonRepository $livraisonRepository, StatutLivraisonRepository $statutLivraisonRepository,CoursierRepository $coursierRepository): Response
    {
        $ann = $statutLivraisonRepository->findBy(['status_title' => "annulée"]);
        $att = $statutLivraisonRepository->findBy(['status_title' => "en attente"]);
        $nb=0;
        $prix=0;
        $prixTotale=0;
        $livraisons = [];

        foreach ($ann as $item) {
            $liv = $item->getLivraison();
            $livraisons[] = $liv;
            $prix= $liv->getPrixTotaleLivraison();
            $prixTotale += $prix ;
            $nb++;
        }

        foreach ($att as $item) {
            $liv = $item->getLivraison();
            $livraisons[] = $liv;
            $nb++;
            $prix= $liv->getPrixTotaleLivraison();
            $prixTotale += $prix ;
        }
        
    
        return $this->render('gerer_tour/index.html.twig', [
            'livraisons' => $livraisons, 
            'prixTotale' => $prixTotale,
            'nbLivraisons' => $nb,
            'coursiers' => $coursierRepository->findAll(),
        ]);
    }

    #[Route('/gerer/data', name: 'app_gerer_data', methods: ['GET'])]
    public function data(LivraisonRepository $livraisonRepository): JsonResponse
    {
        $livraisons = $livraisonRepository->findAll();
        $data = [];
        foreach ($livraisons as $livraison) {
            $tourner = $livraison->getTourner();

            if ($tourner !== null) {
                $data[] = [
                    'id' => $livraison->getId(),
                    'tourner' => [
                        'id' => $tourner->getId(),
                        'prix' => $tourner->getPrixTourner(),
                        // Add other properties as needed
                    ],
                ];
            } else {
                // Handle the case where $tourner is null
                $data[] = [
                    'id' => $livraison->getId(),
                    'tourner' => null,
                ];
            }
        }
        return $this->json($data);
    }


#[Route('/available/couriers/{id}', name: 'app_couriers', methods: ['GET'])]
public function show(Request $request,LivraisonRepository $livraisonRepository, ClientRepository $clientRepository,AdresseRepository $adresseRepository,StatutCoursierRepository $statutCoursierRepository,CoursierRepository $coursierRepository,$id): Response
{
    // $client = $clientRepository->findBy();
    $livraisonId = $request->get('livraisonId');

    // $client=$clientRepository->findBy(['id' => $id]);
    // $idclient = $client[0]->getId();
    // $realAdr=$client[0]->getAdresses();
    // $idAdr=$realAdr[0]->getId();
    // $adr = $adresseRepository->findBy(['id' => $idAdr]);
    // $regionAdr = $adr[0]->getRegion();


 
    $livraison = $livraisonRepository->findOneBy(['id'=>$livraisonId]);
    $idaddress=$livraison->getAddressId();
    $adr = $adresseRepository->findBy(['id' => $idaddress]);
    $regionAdr = $adr[0]->getRegion();

    $dateLivraison = $livraison->getLivraisonDate();
    $dateLivrFormatted = $dateLivraison->format('Y-m-d');
    
    $coursierDispo = $statutCoursierRepository->findBy(['region' => $regionAdr,'titre_statut' => 'disponible','debut_tourner'=>$dateLivraison]);
    
    return $this->render('gerer_tour/couriers.html.twig', [
        'couriers' => $coursierDispo,
        'regionAdr' => $regionAdr,
        'dateLivraison' =>$dateLivrFormatted,
    ]);
}


    #[Route('/affect/{id}', name: 'affectRoute', methods: ['GET', 'POST'])]
    public function affecterAuRoute(Request $request,TournerRepository $tournerRepository,CoursierRepository $coursierRepository,$id, EntityManagerInterface $entityManager,LivraisonRepository $livraisonRepository,StatutLivraisonRepository $statutLivraisonRepository,StatutCoursierRepository $statutCoursierRepository): Response
    {
        $ann = $statutLivraisonRepository->findBy(['status_title' => "annulée"]);
        $att = $statutLivraisonRepository->findBy(['status_title' => "en attente"]);
        $livraisons = $livraisonRepository->findAll();
        $livraisons = [];
        foreach ($ann as $item) {
            $livraisons[] = $item->getLivraison();
        }

        foreach ($att as $item) {
            $livraisons[] = $item->getLivraison();
        }
        $livraisonId121 = $request->get('livraisonId');
        $livraison121 = $livraisonRepository->findOneBy(['id' => $livraisonId121]);
        $datess= $livraison121->getLivraisonDate();
        $coursier=$tournerRepository->findBy(['coursier' => $id,'date'=>$datess]);
        $livraisonId = $request->get('livraisonId');
        if (empty($coursier)) {
            $tour = new Tourner();  
            $livraison = $livraisonRepository->findOneBy(['id' => $livraisonId]);
            $prix= $livraison->getPrixTotaleLivraison();
            $poid = $livraison->getPoidLivraison();
            $coursierN = $coursierRepository->findOneBy(['id' => $id]);
            $tour->setCoursier($coursierN);
            $tour->setPrixTourner($prix);
            $tour->setPoidTourner($poid);
            $tour->setNbLivraison(1);
            $tour->setStatutTourner('en cours');
            $tour->setDate($datess);
            $entityManager->persist($tour);
            $entityManager->flush();
            $livraison->setTourner($tour);
            $changestat = $statutLivraisonRepository->findOneBy(['livraison' => $livraisonId]);
         
            $changestat->setStatusTitle('affecte');
            $entityManager->flush();
            return $this->redirectToRoute('app_gerer_tour');
        } else {
            $livraison = $livraisonRepository->findOneBy(['id' => $livraisonId]);
            $prix= $livraison->getPrixTotaleLivraison();
            $poid = $livraison->getPoidLivraison();
            $tour = $tournerRepository -> findOneBy(['coursier' => $id,'date'=>$datess]);
            $nb=$tour->getNbLivraison();
            $poidTour = $poid + $tour->getPoidTourner();
            $prixTour = $prix + $tour->getPrixTourner();
            $tour -> setPoidTourner($poidTour);
            $tour -> setPrixTourner($prixTour);
            $tour -> setNbLivraison($nb+1);
            $tour->setStatutTourner('en cours');
            $entityManager->flush();
            $livraison->setTourner($tour);
            $changestat = $statutLivraisonRepository->findOneBy(['livraison' => $livraisonId]);
            $changestat->setStatusTitle('affecte');
            $entityManager->flush();
            
            if($nb+1>=12){
                $changestat = $statutCoursierRepository->findOneBy(['coursier' => $id]); 
                $changestat->setTitreStatut('complet');
                $entityManager->persist($changestat);
                $entityManager->flush();
            }
            return $this->redirectToRoute('app_gerer_tour');
        }  

        // $livraisonId = $request->get('livraisonId');
        // $livraison = $livraisonRepository->findOneBy(['id' => $livraisonId]);
        // $datess= $livraison->getLivraisonDate();
    }
    
    #[Route('/available/couriers/{id}', name: 'app_courier_tour', methods: ['GET'])]
    public function showCoursierTour(Request $request,LivraisonRepository $livraisonRepository,TournerRepository $tournerRepository,CoursierRepository $coursierRepository,$id, AdresseRepository $adresseRepository): Response
    {
        $coursier=$tournerRepository->findBy(['coursier' => $id]);
        $coursierTour = $tournerRepository->findBy(['coursier_id' => $id]);
        $livraisons=[];
        foreach($coursierTour as $item){
            $livraisons[] = $item->getLivraisons();
        }
        return $this->render('gerer_tour/index.html.twig', [
            'livraisons' => $livraisons, 
            'couriers' => $coursier, 
        ]);
    }

        #[Route('/history', name: 'history')]
        public function history(Request $request,EntityManagerInterface $entityManager,LivraisonHistoryRepository $livraisonHistoryRepository,LivraisonRepository $livraisonRepository,AdresseRepository $adresseRepository,ClientRepository $clientRepository,CoursierRepository $coursierRepository): Response
        {   
            $Display='';
            $livraisons = $livraisonHistoryRepository->findAll();
            $livDeRegion = [];
            $prix=0;
            $nb=0;
            $region = $request->get('region');
            $coursierUN = $request->get('coursier');
            // $dateLivraison = $request->get('date');
            // $selectedDate = $request->get('date');
            // $selectedDate = DateTime::createFromFormat('Y-m-d\TH:i', $dateLivraison);
            $selectedDate = $request->get('date');
            foreach ($livraisons as $item) {
                $coursier = $item->getCoursier();
                $liv = $item->getLivraison();
                $client = $liv->getClient();
                
                $dateLivr = $item->getDateAjout();
                $dateLivrFormatted = $dateLivr->format('Y-m-d');

                $ClientAdr = $adresseRepository->findOneBy(['client' => $client]);
                if ($coursierUN == '' && $region != '' && $selectedDate =='') {
                    if ($ClientAdr && $ClientAdr->getRegion() == $region) {
                        $livDeRegion[] = $item;
                        $Display = 'Liste des livraisons livrées à ' . $region; 
                        $prix=$prix+$liv->getPrixTotaleLivraison();
                        $nb++;
                    }
                }

                if ($coursierUN != '' && $region == '' && $selectedDate =='') {
                    if ($coursier->getUsername() == $coursierUN) {
                        $livDeRegion[] = $item;
                        $Display = 'Liste des livraisons livrées par ' . $coursierUN;
                        $prix=$prix+$liv->getPrixTotaleLivraison();
                        $nb++;
                    }
                }

                if ($coursierUN != '' && $region != '' && $selectedDate =='') {
                    if ($coursier->getUsername() == $coursierUN && $ClientAdr->getRegion() == $region ) {
                        $livDeRegion[] = $item;
                        $Display = 'Liste des livraisons livrées par ' . $coursierUN . ' à ' . $region;
                        $prix=$prix+$liv->getPrixTotaleLivraison();
                        $nb++;
                    }
                }
                
                if ($coursierUN != '' && $region != '' && $selectedDate !='') {
                    if ($coursier->getUsername() == $coursierUN && $ClientAdr->getRegion() == $region && $dateLivrFormatted==$selectedDate ) {
                        $livDeRegion[] = $item;
                        $Display = 'Liste des livraisons livrées par ' . $coursierUN . ' à ' . $region . ' en ' . $dateLivrFormatted;
                        $prix=$prix+$liv->getPrixTotaleLivraison();
                        $nb++;
                    }
                } 

                if ($coursierUN == '' && $region != '' && $selectedDate !='') {
                    if ($ClientAdr->getRegion() == $region && $dateLivrFormatted==$selectedDate ) {
                        $livDeRegion[] = $item;
                        $Display = 'Liste des livraisons livrées à ' . $region . ' en ' . $dateLivrFormatted ;
                        $prix=$prix+$liv->getPrixTotaleLivraison();
                        $nb++;
                    }
                } 

                if ($coursierUN != '' && $region == '' && $selectedDate !='') {
                    if ( $coursier->getUsername() == $coursierUN && $dateLivrFormatted==$selectedDate ) {
                        $livDeRegion[] = $item;
                        $Display = 'Liste des livraisons livrées par ' . $coursierUN . ' en ' . $dateLivrFormatted ;
                        $prix=$prix+$liv->getPrixTotaleLivraison();
                        $nb++;
                    }
                } 

                if ($coursierUN == '' && $region == '' && $selectedDate !='') {
                    if ($dateLivrFormatted==$selectedDate ) {
                        $livDeRegion[] = $item;
                        $Display = 'Liste des livraisons livrées en ' . $dateLivrFormatted ;
                        $prix=$prix+$liv->getPrixTotaleLivraison();
                        $nb++;
                    }
                } 


            }

            return $this->render('gerer_tour/history.html.twig', [
                'livraisons' => $livDeRegion, 
                'display'=> $Display,
                'coursiers'=> $coursierRepository->findAll(),
                'prix'=>$prix,
                'nb'=>$nb,
            ]);}


    
}
