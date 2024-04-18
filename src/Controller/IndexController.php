<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;


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
        'user' => $currentUser
    ]); 
}



      #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('client/cotact.html.twig', [
            'controller_name' => 'ClientController',
        ]);
    }
   
   
}