<?php

namespace App\Entity;

use App\Repository\RaisonSignalementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RaisonSignalementRepository::class)]
class RaisonSignalement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $raison = null;

    #[ORM\OneToMany(mappedBy: 'raison_signalement', targetEntity: StatutLivraison::class)]
    private Collection $statutLivraisons;

    public function __construct()
    {
        $this->statutLivraisons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaison(): ?string
    {
        return $this->raison;
    }

    public function setRaison(string $raison): static
    {
        $this->raison = $raison;

        return $this;
    }

    /**
     * @return Collection<int, StatutLivraison>
     */
    public function getStatutLivraisons(): Collection
    {
        return $this->statutLivraisons;
    }

    public function addStatutLivraison(StatutLivraison $statutLivraison): static
    {
        if (!$this->statutLivraisons->contains($statutLivraison)) {
            $this->statutLivraisons->add($statutLivraison);
            $statutLivraison->setRaisonSignalement($this);
        }

        return $this;
    }

    public function removeStatutLivraison(StatutLivraison $statutLivraison): static
    {
        if ($this->statutLivraisons->removeElement($statutLivraison)) {
            // set the owning side to null (unless already changed)
            if ($statutLivraison->getRaisonSignalement() === $this) {
                $statutLivraison->setRaisonSignalement(null);
            }
        }

        return $this;
    }
}
