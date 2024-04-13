<?php

namespace App\Entity;

use App\Repository\RegionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegionRepository::class)]
class Region
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'region', targetEntity: StatutCoursier::class)]
    private Collection $region;

    #[ORM\OneToMany(mappedBy: 'region', targetEntity: Adresse::class)]
    private Collection $adresses;

    public function __construct()
    {
        $this->region = new ArrayCollection();
        $this->adresses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, StatutCoursier>
     */
    public function getRegion(): Collection
    {
        return $this->region;
    }

    public function addRegion(StatutCoursier $region): static
    {
        if (!$this->region->contains($region)) {
            $this->region->add($region);
            $region->setRegion($this);
        }

        return $this;
    }

    public function removeRegion(StatutCoursier $region): static
    {
        if ($this->region->removeElement($region)) {
            // set the owning side to null (unless already changed)
            if ($region->getRegion() === $this) {
                $region->setRegion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Adresse>
     */
    public function getAdresses(): Collection
    {
        return $this->adresses;
    }

    public function addAdress(Adresse $adress): static
    {
        if (!$this->adresses->contains($adress)) {
            $this->adresses->add($adress);
            $adress->setRegion($this);
        }

        return $this;
    }

    public function removeAdress(Adresse $adress): static
    {
        if ($this->adresses->removeElement($adress)) {
            // set the owning side to null (unless already changed)
            if ($adress->getRegion() === $this) {
                $adress->setRegion(null);
            }
        }

        return $this;
    }
}
