<?php

namespace App\Entity;

use App\Repository\CoursierPositionHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoursierPositionHistoryRepository::class)]
class CoursierPositionHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $position_history_lat = null;

    #[ORM\Column(nullable: true)]
    private ?float $position_history_long = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $position_date_ajout = null;

    #[ORM\ManyToOne(inversedBy: 'coursierPositionHistories')]
    private ?Coursier $coursier = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPositionHistoryLat(): ?float
    {
        return $this->position_history_lat;
    }

    public function setPositionHistoryLat(?float $position_history_lat): static
    {
        $this->position_history_lat = $position_history_lat;

        return $this;
    }

    public function getPositionHistoryLong(): ?float
    {
        return $this->position_history_long;
    }

    public function setPositionHistoryLong(?float $position_history_long): static
    {
        $this->position_history_long = $position_history_long;

        return $this;
    }

    public function getPositionDateAjout(): ?\DateTimeInterface
    {
        return $this->position_date_ajout;
    }

    public function setPositionDateAjout(\DateTimeInterface $position_date_ajout): static
    {
        $this->position_date_ajout = $position_date_ajout;

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
}
