<?php

namespace App\Entity;

use App\Repository\CoursierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoursierRepository::class)]
class Coursier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $phone = null;

    #[ORM\OneToOne(mappedBy: 'coursier', cascade: ['persist', 'remove'])]
    private ?StatutCoursier $statutCoursier = null;

    #[ORM\OneToMany(targetEntity: CoursierPositionHistory::class, mappedBy: 'coursier')]
    private Collection $coursierPositionHistories;

    #[ORM\OneToMany(targetEntity: Tourner::class, mappedBy: 'coursier')]
    private Collection $tourners;

    public function __construct()
    {
        $this->coursierPositionHistories = new ArrayCollection();
        $this->tourners = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getStatutCoursier(): ?StatutCoursier
    {
        return $this->statutCoursier;
    }

    public function setStatutCoursier(?StatutCoursier $statutCoursier): static
    {
        // unset the owning side of the relation if necessary
        if ($statutCoursier === null && $this->statutCoursier !== null) {
            $this->statutCoursier->setCoursier(null);
        }

        // set the owning side of the relation if necessary
        if ($statutCoursier !== null && $statutCoursier->getCoursier() !== $this) {
            $statutCoursier->setCoursier($this);
        }

        $this->statutCoursier = $statutCoursier;

        return $this;
    }

    /**
     * @return Collection<int, CoursierPositionHistory>
     */
    public function getCoursierPositionHistories(): Collection
    {
        return $this->coursierPositionHistories;
    }

    public function addCoursierPositionHistory(CoursierPositionHistory $coursierPositionHistory): static
    {
        if (!$this->coursierPositionHistories->contains($coursierPositionHistory)) {
            $this->coursierPositionHistories->add($coursierPositionHistory);
            $coursierPositionHistory->setCoursier($this);
        }

        return $this;
    }

    public function removeCoursierPositionHistory(CoursierPositionHistory $coursierPositionHistory): static
    {
        if ($this->coursierPositionHistories->removeElement($coursierPositionHistory)) {
            // set the owning side to null (unless already changed)
            if ($coursierPositionHistory->getCoursier() === $this) {
                $coursierPositionHistory->setCoursier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tourner>
     */
    public function getTourners(): Collection
    {
        return $this->tourners;
    }

    public function addTourner(Tourner $tourner): static
    {
        if (!$this->tourners->contains($tourner)) {
            $this->tourners->add($tourner);
            $tourner->setCoursier($this);
        }

        return $this;
    }

    public function removeTourner(Tourner $tourner): static
    {
        if ($this->tourners->removeElement($tourner)) {
            // set the owning side to null (unless already changed)
            if ($tourner->getCoursier() === $this) {
                $tourner->setCoursier(null);
            }
        }

        return $this;
    }
}
