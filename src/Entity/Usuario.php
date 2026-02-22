<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    private string $nombre;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    private string $email;

    #[ORM\Column(type: "string")]
    private string $password;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $fechaRegistro;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $biografia;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $fotoPerfil = null;

    #[ORM\OneToMany(mappedBy: "usuario", targetEntity: Palabra::class)]
    private Collection $palabras;

    #[ORM\OneToMany(mappedBy: "usuario", targetEntity: Comentario::class)]
    private Collection $comentarios;

    #[ORM\OneToMany(mappedBy: "usuario", targetEntity: Valoracion::class)]
    private Collection $valoraciones;

    #[ORM\OneToMany(mappedBy: "seguidor", targetEntity: Seguimiento::class)]
    private Collection $seguimientosQueHace;

    #[ORM\OneToMany(mappedBy: "seguido", targetEntity: Seguimiento::class)]
    private Collection $seguimientosQueRecibe;

    #[ORM\OneToMany(mappedBy: "remitente", targetEntity: Mensaje::class)]
    private Collection $mensajesEnviados;

    #[ORM\OneToMany(mappedBy: "destinatario", targetEntity: Mensaje::class)]
    private Collection $mensajesRecibidos;


    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }


    public function getFechaRegistro(): \DateTimeInterface
    {
        return $this->fechaRegistro;
    }

    public function setFechaRegistro(\DateTimeInterface $fechaRegistro): self
    {
        $this->fechaRegistro = $fechaRegistro;
        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    #[ORM\Column(type: "json", nullable: true)]
    private ?array $roles = [];

    #[ORM\Column(type: "boolean", nullable: true, options: ["default" => false])]
    private ?bool $isBlocked = false;

    #[ORM\Column(type: "string", length: 100, nullable: true)]
    private ?string $resetToken = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $resetTokenExpiresAt = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $googleId = null;

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;
        return $this;
    }

    public function getResetTokenExpiresAt(): ?\DateTimeInterface
    {
        return $this->resetTokenExpiresAt;
    }

    public function setResetTokenExpiresAt(?\DateTimeInterface $resetTokenExpiresAt): self
    {
        $this->resetTokenExpiresAt = $resetTokenExpiresAt;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles ?? [];
        // garantizamos que el usuario tenga como mínimo el ROL básico
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // Si guardas algún dato temporal y sensible del usuario, límpialo aquí
        // $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function __construct()
    {
        $this->palabras = new ArrayCollection();
        $this->comentarios = new ArrayCollection();
        $this->valoraciones = new ArrayCollection();
        $this->seguimientosQueHace = new ArrayCollection();
        $this->seguimientosQueRecibe = new ArrayCollection();
        $this->mensajesEnviados = new ArrayCollection();
        $this->mensajesRecibidos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFotoPerfil(): ?string
    {
        return $this->fotoPerfil;
    }

    public function setFotoPerfil(?string $fotoPerfil): self
    {
        $this->fotoPerfil = $fotoPerfil;
        return $this;
    }
    /**
     * @return Collection<int, Seguimiento>
     */
    public function getSeguimientosQueHace(): Collection
    {
        return $this->seguimientosQueHace;
    }

    /**
     * @return Collection<int, Seguimiento>
     */
    public function getSeguimientosQueRecibe(): Collection
    {
        return $this->seguimientosQueRecibe;
    }
    public function getBiografia(): ?string
    {
        return $this->biografia;
    }

    public function setBiografia(?string $biografia): self
    {
        $this->biografia = $biografia;
        return $this;
    }

    public function isBlocked(): bool
    {
        return $this->isBlocked ?? false;
    }

    public function setIsBlocked(bool $isBlocked): self
    {
        $this->isBlocked = $isBlocked;
        return $this;
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): self
    {
        $this->googleId = $googleId;
        return $this;
    }

    /**
     * @return Collection<int, Mensaje>
     */
    public function getMensajesEnviados(): Collection
    {
        return $this->mensajesEnviados;
    }

    /**
     * @return Collection<int, Mensaje>
     */
    public function getMensajesRecibidos(): Collection
    {
        return $this->mensajesRecibidos;
    }
}
