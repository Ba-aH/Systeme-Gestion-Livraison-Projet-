<?php

namespace App\Entity;

use App\Repository\LivraisonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LivraisonRepository::class)]
class Livraison
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $code_pin = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $livraison_date = null;

    #[ORM\Column]
    private ?float $frais_livraison = null;

    #[ORM\Column]
    private ?float $prix_totale_livraison = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\Column(nullable: true)]
    private ?float $poid_livraison = null;

    #[ORM\ManyToOne(inversedBy: 'livraisons')]
    private ?Tourner $tourner = null;

    #[ORM\OneToOne(mappedBy: 'livraison', cascade: ['persist', 'remove'])]
    private ?StatutLivraison $statutLivraison = null;

    #[ORM\OneToMany(targetEntity: Colis::class, mappedBy: 'livraison')]
    private Collection $colis;

    public function __construct()
    {
        $this->colis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodePin(): ?int
    {
        return $this->code_pin;
    }

    public function setCodePin(?int $code_pin): static
    {
        $this->code_pin = $code_pin;

        return $this;
    }

    public function getLivraisonDate(): ?\DateTimeInterface
    {
        return $this->livraison_date;
    }

    public function setLivraisonDate(?\DateTimeInterface $livraison_date): static
    {
        $this->livraison_date = $livraison_date;

        return $this;
    }

    public function getFraisLivraison(): ?float
    {
        return $this->frais_livraison;
    }

    public function setFraisLivraison(float $frais_livraison): static
    {
        $this->frais_livraison = $frais_livraison;

        return $this;
    }

    public function getPrixTotaleLivraison(): ?float
    {
        return $this->prix_totale_livraison;
    }

    public function setPrixTotaleLivraison(float $prix_totale_livraison): static
    {
        $this->prix_totale_livraison = $prix_totale_livraison;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getPoidLivraison(): ?float
    {
        return $this->poid_livraison;
    }

    public function setPoidLivraison(?float $poid_livraison): static
    {
        $this->poid_livraison = $poid_livraison;

        return $this;
    }

    public function getTourner(): ?Tourner
    {
        return $this->tourner;
    }

    public function setTourner(?Tourner $tourner): static
    {
        $this->tourner = $tourner;

        return $this;
    }

    public function getStatutLivraison(): ?StatutLivraison
    {
        return $this->statutLivraison;
    }

    public function setStatutLivraison(?StatutLivraison $statutLivraison): static
    {
        // unset the owning side of the relation if necessary
        if ($statutLivraison === null && $this->statutLivraison !== null) {
            $this->statutLivraison->setLivraison(null);
        }

        // set the owning side of the relation if necessary
        if ($statutLivraison !== null && $statutLivraison->getLivraison() !== $this) {
            $statutLivraison->setLivraison($this);
        }

        $this->statutLivraison = $statutLivraison;

        return $this;
    }

    /**
     * @return Collection<int, Colis>
     */
    public function getColis(): Collection
    {
        return $this->colis;
    }

    public function addColi(Colis $coli): static
    {
        if (!$this->colis->contains($coli)) {
            $this->colis->add($coli);
            $coli->setLivraison($this);
        }

        return $this;
    }

    public function removeColi(Colis $coli): static
    {
        if ($this->colis->removeElement($coli)) {
            // set the owning side to null (unless already changed)
            if ($coli->getLivraison() === $this) {
                $coli->setLivraison(null);
            }
        }

        return $this;
    }
}
