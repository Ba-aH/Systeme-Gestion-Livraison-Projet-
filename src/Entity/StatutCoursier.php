<?php

namespace App\Entity;

use App\Repository\StatutCoursierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: StatutCoursierRepository::class)]
#[Broadcast]
class StatutCoursier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $region = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $debut_tourner = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fin_tourner = null;

    #[ORM\OneToOne(inversedBy: 'statutCoursier', cascade: ['persist', 'remove'])]
    private ?Coursier $coursier = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getDebutTourner(): ?\DateTimeInterface
    {
        return $this->debut_tourner;
    }

    public function setDebutTourner(\DateTimeInterface $debut_tourner): static
    {
        $this->debut_tourner = $debut_tourner;

        return $this;
    }

    public function getFinTourner(): ?\DateTimeInterface
    {
        return $this->fin_tourner;
    }

    public function setFinTourner(?\DateTimeInterface $fin_tourner): static
    {
        $this->fin_tourner = $fin_tourner;

        return $this;
    }

    public function getCoursier(): ?Coursier
    {
        return $this->coursier;
    }

    public function setCoursier(?Coursier $coursier): static
    {
        $this->coursier = $coursier;

        return $this;
    }
}
