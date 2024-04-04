<?php

namespace App\Service;

use Lcobucci\JWT\Builder as JwtBuilder;
use Lcobucci\JWT\Signer\Hmac\Sha384;
use Lcobucci\JWT\Signer\Key;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Lcobucci\JWT\BuilderFactory;
use Symfony\Component\Mercure\Jwt\StaticTokenProvider;
use Symfony\Component\Mercure\Jwt\Token;
use Symfony\Component\Mercure\Jwt\Jwt;


class MercureCookieGenerator 
{
    private $tokenStorage;
    private $serializer;
    private $secret;

    public function __construct(TokenStorageInterface $tokenStorage, SerializerInterface $serializer, string $secret)
    {
        $this->tokenStorage = $tokenStorage;
        $this->serializer = $serializer;
        $this->secret = $secret;
    }

    public function generate(): string
{
    // Retrieve the user ID from the token storage
    $userToken = $this->tokenStorage->getToken();
    $id = $userToken->getUserIdentifier();

    // Instantiate StaticTokenProvider with your JWT secret
    $tokenProvider = new StaticTokenProvider($this->secret);

    // Generate a Mercure token
    $jwt = $tokenProvider->getJwt(['mercure' => ['subscribe' => ["http://monsite.com/user/{$id}"]]]);

    // Return the generated token as a string
    return $jwt;
}
}
