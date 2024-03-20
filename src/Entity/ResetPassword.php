<?php

namespace App\Entity;

use App\Repository\ResetPasswordRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResetPasswordRepository::class)]
class ResetPassword
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $codePIN = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $expiredAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodePIN(): ?string
    {
        return $this->codePIN;
    }

    public function setCodePIN(string $codePIN): static
    {
        $this->codePIN = $codePIN;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getExpiredAt(): ?\DateTimeImmutable
    {
        return $this->expiredAt;
    }

    public function setExpiredAt(\DateTimeImmutable $expiredAt): static
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }
    
    /**
     * Automatically set the expiredAt field to the current time plus one hour.
     * @ORM\PrePersist
     */
    public function setDefaultExpiredAt(): void
    {
        if ($this->expiredAt === null) {
            $this->expiredAt = new \DateTimeImmutable('+1 hour');
        }
    }
}
