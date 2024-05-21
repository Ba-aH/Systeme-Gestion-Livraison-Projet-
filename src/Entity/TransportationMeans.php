<?php

namespace App\Entity;

use App\Repository\TransportationMeansRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransportationMeansRepository::class)]
class TransportationMeans
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $capacity = null;

    #[ORM\OneToMany(mappedBy: 'transportMean', targetEntity: Coursier::class)]
    private Collection $coursiers;

    public function __construct()
    {
        $this->coursiers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * @return Collection<int, Coursier>
     */
    public function getCoursiers(): Collection
    {
        return $this->coursiers;
    }

    public function addCoursier(Coursier $coursier): static
    {
        if (!$this->coursiers->contains($coursier)) {
            $this->coursiers->add($coursier);
            $coursier->setTransportMean($this);
        }

        return $this;
    }

    public function removeCoursier(Coursier $coursier): static
    {
        if ($this->coursiers->removeElement($coursier)) {
            // set the owning side to null (unless already changed)
            if ($coursier->getTransportMean() === $this) {
                $coursier->setTransportMean(null);
            }
        }

        return $this;
    }
}
