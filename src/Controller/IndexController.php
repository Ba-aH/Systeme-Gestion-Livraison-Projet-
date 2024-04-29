<?php


namespace App\Controller;

use App\Entity\Client;
use App\Entity\ContactUs;
use App\Repository\AdresseRepository;
use App\Repository\ContactUsRepository;
use App\Repository\LivraisonRepository;
use App\Repository\StatutLivraisonRepository;
use App\Repository\WarehouseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;


class IndexController extends AbstractController
{
    private $tokenStorage;
    private $serializer;

    public function __construct(TokenStorageInterface $tokenStorage,SerializerInterface $serializer)
    {
        $this->tokenStorage = $tokenStorage;
        $this->serializer = $serializer;
        
    }


    #[Route('', name: 'appClient_index')]
public function appClient_index(): Response
{     
    $token = $this->tokenStorage->getToken();
    $currentUser = null; 
    
    if ($token !== null) { 
        $currentUser = $token->getUser();
    }
    
    return $this->render('client/index.html.twig', [
        'user' => $currentUser,
        'error' => null
    ]); 
}



    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('client/cotact.html.twig', [
            'controller_name' => 'ClientController',
        ]);
    }

    #[Route('/contact-us', name: 'contact-us')]
    public function contact_us(Request $request, EntityManagerInterface $entityManager): Response
    {
        $name = $request->get('name');
        $email = $request->get('email');
        $subject = $request->get('subject');
        $message = $request->get('message');
        $contact= new ContactUs;
        $contact->setEmail($email);
        $contact->setName($name);
        $contact->setMessage($subject);
        $contact->setSujet($subject);
        $entityManager->persist($contact);
        $entityManager->flush();
        return $this->render('client/cotact.html.twig', [
            'controller_name' => 'ClientController',
        ]);
    }
   
    #[Route('/check-order', name: 'CheckOrder')]
    public function CheckOrder(Request $request,LivraisonRepository $livraisonRepository): Response
    {
        $ref = $request->get('reference');
        $livraison = $livraisonRepository->findOneBy(['reference'=>$ref]);

        return $this->render('client/consult.html.twig', [
            'liv' =>  $livraison,'address' =>null ,'error'=>null,'stat'=>null
        ]);
    }

    #[Route('/rechLivParRef', name: 'rechLivParRef', methods: ['GET'])]
    public function rechLivParRef(Request $request ,WarehouseRepository $warehouseRepository,StatutLivraisonRepository $statutLivraisonRepository,LivraisonRepository $livraisonRepository,EntityManagerInterface $entityManager,AdresseRepository $adresseRepository): Response
    {
        $token = $this->tokenStorage->getToken();
        $coursier=null;
        $currentUser=null;
        if ($token){
            $currentUser = $token->getUser();
            if ($currentUser instanceof Client) {
                $identifier = $currentUser->getUserIdentifier();
                $id = $currentUser->getId();
            }
        }
        
        $livraisonReference = $request->get('reference');
        $liv = $livraisonRepository->findOneBy(['reference' =>  $livraisonReference]);
        if ($liv){
            $address=$adresseRepository->findOneBy(['id' => $liv->getAddressId()]);
            $statut=$statutLivraisonRepository->findOneBy(['livraison' =>  $liv]);
            $regionAdress=$address->getRegion();
            $warehouse = $warehouseRepository->findOneBy(['region'=>$regionAdress]);

            if ($statut->getStatusTitle()=='affecté' or $statut->getStatusTitle()=='proche' or $statut->getStatusTitle()=='confirmé' or $statut->getStatusTitle()=='en cours'){
                $coursier=$liv->getTourner()->getCoursier();
                $email=$coursier->getEmail();
            }
        }
        if ($liv){
            return $this->render('client/livraisonParReference.html.twig', [
                'user' => $currentUser,
                'liv' =>  $liv,
                'coursier' =>$coursier ,
                'adress' => $address,
                'warehouse' => $warehouse,
                'stat'=>$statut
            ]);
        }
        else{
            return $this->render('client/index.html.twig', [
                'user' => $currentUser,
                'error' => 'Il n y a pas de livraison correspondant à cette référence.'
            ]);  
        }
    }
   
}