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
use App\Repository\RaisonsEchecRepository;
use App\Entity\Tourner;
use App\Entity\StatutLivraison;
use App\Entity\Livraison;
#[Route('/coursier')]
class CoursierController extends AbstractController
{
    #[Route('/test', name: 'test', methods: ['GET'])]
    public function index(LivraisonRepository $livraisonRepository): Response
    {
        return $this->render('coursier/confrm.html.twig');
    }
    #[Route('/details/{id}', name: 'details', methods: ['GET'])]
    public function details(Request $request ,LivraisonRepository $livraisonRepository,EntityManagerInterface $entityManager,AdresseRepository $adresseRepository): Response
    {
        $livraisonId = $request->get('id');
        $liv = $entityManager->getRepository(Livraison::class)->findOneBy(['id' =>  $livraisonId]);
        $client=$liv->getClient();
        $address=$adresseRepository->findBy(['id' =>    $liv->getAddressId()]);
        return $this->render('coursier/details.html.twig', [
            'liv' =>  $liv,'client' =>$client,'address' =>$address
        ]);
    }


    #[Route('/afficher_tournees', name: 'afficher_tournees', methods: ['GET'])]
    public function afficher_tournees(EntityManagerInterface $entityManager,TournerRepository $tournerRepository,LivraisonRepository $livraisonRepository): Response
    {
    $idcoursier=1;
    $tabtourner=$tournerRepository->findBy(['coursier' => 1,'statut_tourner'=>'²']);
    


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
       

    #[Route('/echecc/{id}', name: 'echecc', methods: ['GET'])]
    public function echecc(Request $request,RaisonsEchecRepository $raisonsEchecRepository ,LivraisonRepository $livraisonRepository,EntityManagerInterface $entityManager,AdresseRepository $adresseRepository): Response
    {
        $livraisonId = $request->get('id');

        $listraisons=$raisonsEchecRepository->findAll();
       
        return $this->render('coursier/echec.html.twig', [
            'livraisonId' =>  $livraisonId,  'list' =>  $listraisons,
        ]);
    }
    #[Route('/confirmer/{id}', name: 'confirmer', methods: ['GET'])]
    public function confirmer(Request $request,RaisonsEchecRepository $raisonsEchecRepository ,LivraisonRepository $livraisonRepository,EntityManagerInterface $entityManager,AdresseRepository $adresseRepository): Response
    {
        $livraisonId = $request->get('id');

       
       
        return $this->render('coursier/confrm.html.twig', [
            'livraisonId' =>  $livraisonId
        ]);
    }
    #[Route('/confirmsubmit', name: 'confirmsubmit', methods: ['post'])]
    public function confirmsubmit(Request $request ,StatutLivraisonRepository $statutLivraison,EntityManagerInterface $entityManager,AdresseRepository $adresseRepository): Response
    {  
        $livraisonId = $request->get('livraisonId');
        $livraison= $entityManager->getRepository(Livraison::class)->findOneBy(['id' =>  $livraisonId]);
        $livstat= $entityManager->getRepository(StatutLivraison::class)->findOneBy(['livraison' => $livraisonId]);
        $submitButton = $request->request->get('submit_button');
        if ($submitButton === 'normal') {
            $livstat->setStatusTitle('confirmé');
        } elseif ($submitButton === 'pin') {
           
        }


            $entityManager->flush();
            return $this->redirectToRoute('afficher_tournees');
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
            return $this->redirectToRoute('afficher_tournees');
    }

    #[Route('/coursier_annulation/{id}', name: 'coursier_annulation', methods: ['GET'])]
    public function coursier_annulation(Request $request,RaisonsEchecRepository $raisonsEchecRepository ,LivraisonRepository $livraisonRepository,EntityManagerInterface $entityManager,AdresseRepository $adresseRepository): Response
    {
        $livraisonId = $request->get('id');
        $livraison= $entityManager->getRepository(Livraison::class)->findOneBy(['id' =>  $livraisonId]);
        $livstat= $entityManager->getRepository(StatutLivraison::class)->findOneBy(['livraison' => $livraisonId]);
        $livstat->setStatusTitle('annulée');
        $livraison->setTourner(null);
        // $livstat->setNote(null);
        $entityManager->flush();
        return $this->redirectToRoute('afficher_tournees');
    }


  
}
