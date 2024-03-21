<?php

namespace App\Entity;

use App\Repository\CoursierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\DBAL\Types\Types;


#[ORM\Entity(repositoryClass: CoursierRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Coursier  implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $phone = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_ajout = null;
   
    
    #[ORM\OneToMany(targetEntity: CoursierPositionHistory::class, mappedBy: 'coursier')]
    private Collection $coursierPositionHistories;

    
    #[ORM\OneToMany(targetEntity: Tourner::class, mappedBy: 'coursier')]
    private Collection $tourners;

   
    #[ORM\OneToMany(mappedBy: 'coursier', targetEntity: StatutCoursier::class)]
    private Collection $statutCoursiers;

   


    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;


    public function __construct()
    {
        $this->coursierPositionHistories = new ArrayCollection();
        $this->tourners = new ArrayCollection();
        $this->statutCoursiers = new ArrayCollection();
       
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
    
    public function getDateAjout(): ?\DateTimeInterface
    {
        return $this->date_ajout;
    }

    public function setDateAjout(\DateTimeInterface $date_ajout): static
    {
        $this->date_ajout = $date_ajout;

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

    /**
     * @return Collection<int, StatutCoursier>
     */
    public function getStatutCoursiers(): Collection
    {
        return $this->statutCoursiers;
    }

    public function addStatutCoursier(StatutCoursier $statutCoursier): static
    {
        if (!$this->statutCoursiers->contains($statutCoursier)) {
            $this->statutCoursiers->add($statutCoursier);
            $statutCoursier->setCoursier($this);
        }

        return $this;
    }

    public function removeStatutCoursier(StatutCoursier $statutCoursier): static
    {
        if ($this->statutCoursiers->removeElement($statutCoursier)) {
            // set the owning side to null (unless already changed)
            if ($statutCoursier->getCoursier() === $this) {
                $statutCoursier->setCoursier(null);
            }
        }

        return $this;
    }

   

    

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    
}
