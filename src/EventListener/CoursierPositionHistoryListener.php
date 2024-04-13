<?php

namespace App\EventListener;

use App\Entity\CoursierPositionHistory;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Mercure\Hub;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\Jwt\StaticTokenProvider;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class CoursierPositionHistoryListener
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function postPersist(CoursierPositionHistory $history, LifecycleEventArgs $args)
    {
        $serializedData = $this->serializer->serialize([
            'cordinates' => $history,
        ], 'json', [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['coursier', 'client', 'adresse', 'tourner'],
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);

        $tokenProvider = new StaticTokenProvider($_ENV['MERCURE_JWT_SECRET']);
        $hub = new Hub($_ENV['MERCURE_URL'], $tokenProvider);

        $update = new Update(
            "http://monsite.com/user",
            $serializedData
        );

        $hub->publish($update);
    }
}