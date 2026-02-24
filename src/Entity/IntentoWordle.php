<?php

namespace App\Entity;

use App\Repository\IntentoWordleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IntentoWordleRepository::class)]
class IntentoWordle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuario = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?RetoDiario $reto = null;

    #[ORM\Column(type: Types::JSON)]
    private array $historial = [];

    #[ORM\Column]
    private ?bool $completado = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): static
    {
        $this->usuario = $usuario;
        return $this;
    }

    public function getReto(): ?RetoDiario
    {
        return $this->reto;
    }

    public function setReto(?RetoDiario $reto): static
    {
        $this->reto = $reto;
        return $this;
    }

    public function getHistorial(): array
    {
        return $this->historial;
    }

    public function setHistorial(array $historial): static
    {
        $this->historial = $historial;
        return $this;
    }

    public function isCompletado(): ?bool
    {
        return $this->completado;
    }

    public function setCompletado(bool $completado): static
    {
        $this->completado = $completado;
        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): static
    {
        $this->fecha = $fecha;
        return $this;
    }
}
