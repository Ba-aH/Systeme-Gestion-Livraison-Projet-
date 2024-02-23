<?php

namespace App\Entity;

use App\Repository\StatutLivraisonRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatutLivraisonRepository::class)]
class StatutLivraison
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $status_title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status_class = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $status_date_ajout = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $status_date_modifier = null;

    #[ORM\OneToOne(inversedBy: 'statutLivraison', cascade: ['persist', 'remove'])]
    private ?Livraison $livraison = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatusTitle(): ?string
    {
        return $this->status_title;
    }

    public function setStatusTitle(string $status_title): static
    {
        $this->status_title = $status_title;

        return $this;
    }

    public function getStatusClass(): ?string
    {
        return $this->status_class;
    }

    public function setStatusClass(?string $status_class): static
    {
        $this->status_class = $status_class;

        return $this;
    }

    public function getStatusDateAjout(): ?\DateTimeInterface
    {
        return $this->status_date_ajout;
    }

    public function setStatusDateAjout(\DateTimeInterface $status_date_ajout): static
    {
        $this->status_date_ajout = $status_date_ajout;

        return $this;
    }

    public function getStatusDateModifier(): ?\DateTimeInterface
    {
        return $this->status_date_modifier;
    }

    public function setStatusDateModifier(?\DateTimeInterface $status_date_modifier): static
    {
        $this->status_date_modifier = $status_date_modifier;

        return $this;
    }

    public function getLivraison(): ?Livraison
    {
        return $this->livraison;
    }

    public function setLivraison(?Livraison $livraison): static
    {
        $this->livraison = $livraison;

        return $this;
    }
}
