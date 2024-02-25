<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoursierDisponibleController extends AbstractController
{
    #[Route('/coursier/disponible', name: 'app_coursier_disponible')]
    public function index(): Response
    {
        return $this->render('coursier_disponible/index.html.twig', [
            'controller_name' => 'CoursierDisponibleController',
        ]);
    }
}
