<?php

namespace App\Controller;

use App\Entity\Administrateur;
use App\Entity\CoursierPositionHistory;
use App\Repository\AdresseRepository;
use App\Repository\CoursierPositionHistoryRepository;
use App\Repository\CoursierRepository;
use App\Repository\LivraisonRepository;
use App\Repository\TournerRepository;
use App\Repository\WarehouseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\Hub;
use Symfony\Component\Mercure\Update;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Jwt\StaticTokenProvider;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Event\LifecycleEventArgs;

class TrackingController extends AbstractController{

    private $tokenStorage;
    private $entityManager;

    public function __construct(TokenStorageInterface $tokenStorage,EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;

    }

    
    #[Route('/ping', name: 'ping', methods: ['POST'])]
    public function ping(): Response
    {
        $token = $this->tokenStorage->getToken();
        $currentUser = $token->getUser();
        if ($currentUser instanceof Administrateur) {
            $email = $currentUser->getEmail();  
        }
    
        $tokenProvider = new StaticTokenProvider($_ENV['MERCURE_JWT_SECRET']);

        $hub = new Hub($_ENV['MERCURE_URL'], $tokenProvider);
        
        
        $update = new Update(
            "http://monsite.com/user",
            json_encode([]),
            $data='' 
        );

        
        $hub->publish($update);

        return $this->redirectToRoute('app_home');
    }

    #[Route('/tracking/{idc}/{idt}', name: 'coursier_tracking')]
    public function tourCoursierTrack(Request $request, CoursierRepository $coursierRepository, TournerRepository $tournerRepository, CoursierPositionHistoryRepository $coursierPositionHistory, SerializerInterface $serializer, $idc, $idt, TokenStorageInterface $tokenStorage): Response
    {
        // Retrieve all position history entries for the given tourner and coursier
        $lat_long =  $coursierPositionHistory->findBy(['tourner' => $idt, 'coursier' => $idc]);

        // Check if any position history entry exists
        if (empty($lat_long)) {
            return new JsonResponse(['message' => 'No position history found'], Response::HTTP_NOT_FOUND);
        }

        // Get the last position history entry
        $lastPosition = end($lat_long);

        // Serialize the last position history entry
        $data = $serializer->serialize($lastPosition, 'json', [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['coursierPositionHistories', 'coursier', 'client', 'adresse', 'tourner'], // Exclude unnecessary fields
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);

        $token = $tokenStorage->getToken();
        $currentUser = $token->getUser();
        if ($currentUser instanceof Administrateur) {
            $email = $currentUser->getEmail();
        }

        $tokenProvider = new StaticTokenProvider($_ENV['MERCURE_JWT_SECRET']);
        $hub = new Hub($_ENV['MERCURE_URL'], $tokenProvider);

        $update = new Update(
            "http://monsite.com/user/{$email}",
            $data
        );

        $hub->publish($update);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/addLocationHistory', name: 'addLocationHistory')]
    public function addLocation(Request $request, EntityManagerInterface $entityManager,TournerRepository $tournerRepository,CoursierRepository $coursierRepository, CoursierPositionHistoryRepository $coursierPositionHistoryRepository)
    {
        $lat = doubleval($request->get('positionHistoryLat'));
        $long = doubleval($request->get('positionHistoryLong'));
        $c = $request->get('coursier');
        $t = $request->get('tourner');
        $cph = new CoursierPositionHistory();
        $cph->setPositionHistoryLat($lat);
        $cph->setPositionHistoryLong($long);
        $coursier = $coursierRepository->findOneBy(['id' => $c]);
        $tourner = $tournerRepository->findOneBy(['id' => $t]);
        $cph->setCoursier($coursier);
        $cph->setTourner($tourner);
        $cph->setPositionDateAjout(new \DateTime());
        if ($lat&&$long&&$coursier&&$tourner){
        $entityManager->persist($cph);
        $entityManager->flush();
     }      
    }

    #[Route('/currentDeliverys', name: 'currentDeliverys')]
    public function currentDeliverys(Request $request,AdresseRepository $adresseRepository,
    CoursierRepository $coursierRepository,WarehouseRepository $warehouseRepository,
    LivraisonRepository $livraisonRepository, TournerRepository $tournerRepository, CoursierPositionHistoryRepository $coursierPositionHistory, SerializerInterface $serializer, TokenStorageInterface $tokenStorage):Response
    {
        // $currentDate = new \DateTime(); to change later 
        $currentDate = new \DateTime('2024-04-12');
        $livraisons = $livraisonRepository->findBy(['livraison_date'=> $currentDate]);
        $tours=$tournerRepository->findBy(['date'=> $currentDate]);
        $data = [];
        foreach ($tours as $tour) {
            $tourID= $tour->getId();
            $livraisons=$livraisonRepository->findBy(['tourner' =>$tourID]);

            foreach ($livraisons as $livraison) {
                $adrID= $livraison->getAddressId();
                $adrClient=$adresseRepository->findOneBy(['id' =>$adrID]);
                $warehouse= $warehouseRepository->findOneBy(['region'=>$adrClient->getRegion()]);
            $data[] = [
                'tour' => $livraison->getTourner()->getId(),
                'warehouse' => [
                    'latitude' => $warehouse->getLatitude(),
                    'longtitude' => $warehouse->getLongitude(),
                ],
                'client' => [
                    'latitude' => $adrClient->getLatitude(),
                    'longtitude' => $adrClient->getLongitude(),
                ],
            ];
        }
        }
        // $data1 = $serializer->serialize($data, 'json', [
        //     AbstractNormalizer::IGNORED_ATTRIBUTES => ['tourner', 'coursier', 'client', 'adresse', 'tourner'], // Exclude unnecessary fields
        //     AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
        //         return $object->getId();
        //     }
        // ]);
        return $this->json($data);
    }

    #[Route('/tourDeleveries/{id}', name: 'tourDeleveries')]
    public function tourDeleveries($id,Request $request,AdresseRepository $adresseRepository,
    CoursierRepository $coursierRepository,WarehouseRepository $warehouseRepository,
    LivraisonRepository $livraisonRepository, TournerRepository $tournerRepository, CoursierPositionHistoryRepository $coursierPositionHistory, SerializerInterface $serializer, TokenStorageInterface $tokenStorage):Response
    {
        // $currentDate = new \DateTime(); to change later 
        $currentDate = new \DateTime('2024-04-12');
        $livraisons = $livraisonRepository->findBy(['tourner'=> $id]);
        $tour=$tournerRepository->findOneBy(['id'=> $id]);
        
            $tourID= $tour->getId();
            $coursierId= $tour->getCoursier()->getId();
            $coursierEmail= $tour->getCoursier()->getEmail();
            $livraisons=$livraisonRepository->findBy(['tourner' =>$tourID]);
            $warehouseAdded = false;
            $data[] = [
                'coursier' => $coursierId
            ];
            $data[] = [
                'tourId' => $tourID
            ];
            foreach ($livraisons as $livraison) {
                $adrID= $livraison->getAddressId();
                $adrClient=$adresseRepository->findOneBy(['id' =>$adrID]);
                $warehouse= $warehouseRepository->findOneBy(['region'=>$adrClient->getRegion()]);
                if (!$warehouseAdded) {
                    $data[] = [
                        'warehouse' => [
                            'latitude' => $warehouse->getLatitude(),
                            'longtitude' => $warehouse->getLongitude(),
                            'region' => $warehouse->getNom(),
                        ],
                    ];
                    $warehouseAdded = true; 
                }
            $data[] = [
                'client' => [
                    'cl' => $livraison->getClient()->getEmail(),
                    'latitude' => $adrClient->getLatitude(),
                    'longtitude' => $adrClient->getLongitude(),
                ],
            ];
          
            
        } 
        
        return $this->json($data);
    }
    #[Route('/tourEncours', name: 'tourEncours')]
    public function tourEncours(Request $request, TournerRepository $tournerRepository, SerializerInterface $serializer, TokenStorageInterface $tokenStorage):Response
    {
        $tours = $tournerRepository->findBy(['statut_tourner'=>'en cours']);
        foreach($tours as $tour){
        $data[] = [
            'tourId' => $tour->getId(),
        ];
        }
        
        return $this->json($data);
    }

}