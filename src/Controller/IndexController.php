<?php


namespace App\Controller;

use App\Entity\ContactUs;
use App\Repository\ContactUsRepository;
use App\Repository\LivraisonRepository;
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
   
}