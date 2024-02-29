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

#[Route('/administrator')]
class GererTourController extends AbstractController
{
    #[Route('/gerer/tour', name: 'app_gerer_tour', methods: ['GET'])]
    public function index(LivraisonRepository $livraisonRepository, StatutLivraisonRepository $statutLivraisonRepository): Response
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
        
    
        return $this->render('gerer_tour/index.html.twig', [
            'livraisons' => $livraisons, 
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
    $client=$clientRepository->findBy(['id' => $id]);
    $idclient = $client[0]->getId();
    $realAdr=$client[0]->getAdresses();
    $idAdr=$realAdr[0]->getId();
    $adr = $adresseRepository->findBy(['id' => $idAdr]);
    $regionAdr = $adr[0]->getRegion();
    $livraisonId = $request->get('livraisonId');
    $livraison = $livraisonRepository->findOneBy(['id'=>$livraisonId]);
    $dateLivraison = $livraison->getLivraisonDate();

    
    $coursierDispo = $statutCoursierRepository->findBy(['region' => $regionAdr,'titre_statut' => 'disponible','debut_tourner'=>$dateLivraison]);
    
    return $this->render('gerer_tour/couriers.html.twig', [
        'couriers' => $coursierDispo,
        'regionAdr' => $regionAdr,
    ]);
}


    #[Route('/affect/{id}', name: 'affectRoute', methods: ['GET', 'POST'])]
    public function affecterAuRoute(Request $request,TournerRepository $tournerRepository,CoursierRepository $coursierRepository,$id, EntityManagerInterface $entityManager,LivraisonRepository $livraisonRepository,StatutLivraisonRepository $statutLivraisonRepository,StatutCoursierRepository $statutCoursierRepository): Response
    {
        $ann = $statutLivraisonRepository->findBy(['status_title' => "annulée"]);
        $att = $statutLivraisonRepository->findBy(['status_title' => "en attend"]);
        $livraisons = $livraisonRepository->findAll();
        $livraisons = [];
        foreach ($ann as $item) {
            $livraisons[] = $item->getLivraison();
        }

        foreach ($att as $item) {
            $livraisons[] = $item->getLivraison();
        }

        $coursier=$tournerRepository->findBy(['coursier' => $id]);
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
            $entityManager->persist($tour);
            $entityManager->flush();
            $livraison->setTourner($tour);
            $changestat = $statutLivraisonRepository->findOneBy(['livraison' => $livraisonId]);
         
            $changestat->setStatusTitle('affecte');
            $entityManager->flush();
            return $this->render('gerer_tour/coursier_tour.html.twig', [
                'livraisons' => $livraisons,   
            ]);
        } else {
            $livraison = $livraisonRepository->findOneBy(['id' => $livraisonId]);
            $prix= $livraison->getPrixTotaleLivraison();
            $poid = $livraison->getPoidLivraison();
            $tour = $tournerRepository -> findOneBy(['coursier' => $id]);
            $nb=$tour->getNbLivraison();
            $poidTour = $poid + $tour->getPoidTourner();
            $prixTour = $prix + $tour->getPrixTourner();
            $tour -> setPoidTourner($poidTour);
            $tour -> setPrixTourner($prixTour);
            $tour -> setNbLivraison($nb+1);
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


            return $this->render('gerer_tour/index.html.twig', [
                // 'courier' => $coursierRepository->findOneBy(['id'=>$id]),
                'livraisons' => $livraisons,   
            ]);
        }  

       
    }
    
    #[Route('/available/couriers/{id}', name: 'app_courier_tour', methods: ['GET'])]
    public function showCoursierTour(Request $request,LivraisonRepository $livraisonRepository,TournerRepository $tournerRepository,CoursierRepository $coursierRepository,$id): Response
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
        public function history(EntityManagerInterface $entityManager,LivraisonHistoryRepository $LivraisonHistoryRepository): Response
        {
        $liv_hist=$LivraisonHistoryRepository->findAll();
        $livraisons = [];

        foreach ($liv_hist as $item) {
                    $livraisons[] = $item->getLivraison();
                }
                return $this->render('gerer_tour/history.html.twig', [
                    'livraisons' => $livraisons, 
                ]);
            }
    
}
