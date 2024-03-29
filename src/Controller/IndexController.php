<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class IndexController extends AbstractController
{
    


    #[Route('', name: 'app_index')]
    public function Appindex(): Response
     {         
     return $this->render('client/index.html.twig', [
           'controller_name' => 'ClientController',
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