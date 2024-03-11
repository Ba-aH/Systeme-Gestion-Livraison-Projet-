<?php

namespace App\Controller;

use App\Repository\StatutCoursierRepository;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Administrateur;
use App\Entity\Coursier;
use App\Entity\User;
use App\Entity\Client;
use Doctrine\ORM\Cache\Region;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ApiLoginController extends AbstractController
{
    private $tokenStorage;
    private $serializer;

    public function __construct(TokenStorageInterface $tokenStorage,SerializerInterface $serializer,StatutCoursierRepository $statutCoursierRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->serializer = $serializer;
    }

    #[Route('/api/login', name: 'api_login')]
    public function index(): Response
    {
        // Access the token from the TokenStorageInterface
        $token = $this->tokenStorage->getToken();
        $data = [];
        if ($token === null) {
            return $this->json([
                'message' => 'Token not found',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Get the user from the token
        $currentUser = $token->getUser();

        if ($currentUser instanceof Administrateur) {
            $identifier = $currentUser->getUserIdentifier();
            $id = $currentUser->getId();
            $username = $currentUser->getUsername();
            $nom = $currentUser->getNom();
            $prenom = $currentUser->getPrenom();
            $phone = $currentUser->getPhone();
        } elseif ($currentUser instanceof Coursier) {
            $identifier = $currentUser->getUserIdentifier();
            $id = $currentUser->getId();
            $username = $currentUser->getUsername();
            $nom = $currentUser->getNom();
            $prenom = $currentUser->getPrenom();
            $phone = $currentUser->getPhone();
        } elseif ($currentUser instanceof Client) {
            $identifier = $currentUser->getUserIdentifier();
            $id = $currentUser->getId();
            $username = $currentUser->getUsername();
            $nom = $currentUser->getNom();
            $prenom = $currentUser->getPrenom();
            $phone = $currentUser->getPhone();
        } else {
            return $this->json([
                'message' => 'Invalid user type',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Exclude properties causing circular references
        $excludedProperties = ['coursierPositionHistories', 'tourners', 'statutCoursier', 'livraisonHistories'];

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ApiLoginController.php',    
            'id' => $id,
            'email' => $identifier,
            'username' => $username,
            'nom' => $nom,
            'prenom' => $prenom,
            'phone' => $phone,        
        ]);
    }
}
