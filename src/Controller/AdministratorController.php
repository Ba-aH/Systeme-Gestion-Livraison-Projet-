<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ClientRepository;


#[Route('/administrator')]
class AdministratorController extends AbstractController
{
    #[Route('/index', name: 'app_administrator')]
    public function index(): Response
    {
        return $this->render('administrator/index.html.twig', [
            'controller_name' => 'AdministratorController',
        ]);
    }

    #[Route('/clients', name: 'app_colis_index', methods: ['GET'])]
    public function index1(ClientRepository $clientRepository): Response
    {
        return $this->render('administrator/coursier_list.html.twig', [
            'clients' => $clientRepository->findAll(),
        ]);
        
    }
    
}
