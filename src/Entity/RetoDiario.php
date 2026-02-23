<?php

namespace App\Entity;

use App\Repository\RetoDiarioRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RetoDiarioRepository::class)]
class RetoDiario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Palabra $palabra = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $fecha = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPalabra(): ?Palabra
    {
        return $this->palabra;
    }

    public function setPalabra(?Palabra $palabra): static
    {
        $this->palabra = $palabra;

        return $this;
    }

    public function getFecha(): ?\DateTime
    {
        return $this->fecha;
    }

    public function setFecha(\DateTime $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }
}
