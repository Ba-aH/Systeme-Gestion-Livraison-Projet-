<?php

namespace App\Controller;
use App\Repository\LivraisonRepository;
use App\Repository\StatutLivraisonRepository;
use App\Repository\CoursierRepository;

use App\Repository\TournerRepository;
use App\Repository\ClientRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AdresseRepository ;
use App\Entity\Tourner;

use App\Entity\Livraison;
#[Route('/coursier')]
class CoursierController extends AbstractController
{
    #[Route('/test', name: 'test', methods: ['GET'])]
    public function index(LivraisonRepository $livraisonRepository): Response
    {
        return $this->render('coursier/test.html.twig');
    }
    #[Route('/details/{id}', name: 'details', methods: ['GET'])]
    public function details(Request $request ,LivraisonRepository $livraisonRepository,EntityManagerInterface $entityManager,AdresseRepository $adresseRepository): Response
    {
        $livraisonId = $request->get('id');
        $liv = $entityManager->getRepository(Livraison::class)->findOneBy(['id' =>  $livraisonId]);
        $client=$liv->getClient();
        $address=$adresseRepository->findBy(['client' =>    $client->getId()]);
        return $this->render('coursier/details.html.twig', [
            'liv' =>  $liv,'client' =>$client,'address' =>$address
        ]);
    }


    #[Route('/afficher_tournees', name: 'afficher_tournees', methods: ['GET'])]
    public function afficher_tournees(TournerRepository $tournerRepository,LivraisonRepository $livraisonRepository): Response
    {
    $idcoursier=1;
    $tabtourner=$tournerRepository->findBy(['coursier' => 1,'statut_tourner'=>'²']);
    


    $livraisons=[];
    foreach ($tabtourner as $item) {
        $livraisons[] = $livraisonRepository->findBy(['tourner' => $item->getId()]);
    }

        return $this->render('coursier/temp.html.twig', [
            'livraisons' =>  $livraisons,
        ]);
        
    }
       
  
}
