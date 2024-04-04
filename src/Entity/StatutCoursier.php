<?php

namespace App\Entity;

use App\Repository\StatutCoursierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: StatutCoursierRepository::class)]

class StatutCoursier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

 
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $debut_tourner = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titre_statut = null;

    #[ORM\ManyToOne(inversedBy: 'statutCoursiers')]
    private ?Coursier $coursier = null;

    #[ORM\ManyToOne(inversedBy: 'region')]
    private ?Region $region = null;

    public function getId(): ?int
    {
        return $this->id;
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


   

    public function getTitreStatut(): ?string
    {
        return $this->titre_statut;
    }

    public function setTitreStatut(?string $titre_statut): static
    {
        $this->titre_statut = $titre_statut;

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

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): static
    {
        $this->region = $region;

        return $this;
    }
}
