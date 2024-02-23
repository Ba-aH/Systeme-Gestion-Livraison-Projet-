<?php

namespace App\Entity;

use App\Repository\TournerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TournerRepository::class)]
class Tourner
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $nb_livraison = null;

    #[ORM\Column(nullable: true)]
    private ?float $poid_tourner = null;

    #[ORM\Column]
    private ?float $prix_tourner = null;

    #[ORM\ManyToOne(inversedBy: 'tourners')]
    private ?Coursier $coursier = null;

    #[ORM\OneToMany(targetEntity: Livraison::class, mappedBy: 'tourner')]
    private Collection $livraisons;

    public function __construct()
    {
        $this->livraisons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbLivraison(): ?int
    {
        return $this->nb_livraison;
    }

    public function setNbLivraison(int $nb_livraison): static
    {
        $this->nb_livraison = $nb_livraison;

        return $this;
    }

    public function getPoidTourner(): ?float
    {
        return $this->poid_tourner;
    }

    public function setPoidTourner(?float $poid_tourner): static
    {
        $this->poid_tourner = $poid_tourner;

        return $this;
    }

    public function getPrixTourner(): ?float
    {
        return $this->prix_tourner;
    }

    public function setPrixTourner(float $prix_tourner): static
    {
        $this->prix_tourner = $prix_tourner;

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

    /**
     * @return Collection<int, Livraison>
     */
    public function getLivraisons(): Collection
    {
        return $this->livraisons;
    }

    public function addLivraison(Livraison $livraison): static
    {
        if (!$this->livraisons->contains($livraison)) {
            $this->livraisons->add($livraison);
            $livraison->setTourner($this);
        }

        return $this;
    }

    public function removeLivraison(Livraison $livraison): static
    {
        if ($this->livraisons->removeElement($livraison)) {
            // set the owning side to null (unless already changed)
            if ($livraison->getTourner() === $this) {
                $livraison->setTourner(null);
            }
        }

        return $this;
    }
}
