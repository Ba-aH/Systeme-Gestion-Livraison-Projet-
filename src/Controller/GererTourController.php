<?php

namespace App\Controller;
use App\Repository\LivraisonRepository;
use App\Repository\StatutLivraisonRepository;
use App\Repository\CoursierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/administrator')]
class GererTourController extends AbstractController
{
    #[Route('/gerer/tour', name: 'app_gerer_tour', methods: ['GET'])]
    public function index(LivraisonRepository $livraisonRepository, StatutLivraisonRepository $statutLivraisonRepository): Response
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

#[Route('available/couriers/{id}', name: 'app_couriers', methods: ['GET'])]
public function show(CoursierRepository $coursierRepository): Response
{
    
    $couriers = $coursierRepository->findAll();
    return $this->render('gerer_tour/couriers.html.twig', [
        'couriers' => $couriers,
    ]);
}
      
    
}
