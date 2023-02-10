<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Service\FileUploader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"username"}, message="Existe un usuario registrado con este nombre")
 * @UniqueEntity(fields={"e_mail"}, message="Existe un correo registrado con este nombre")
 */
class User implements UserInterface
{
    const REG_SUCCESS = 'Se ha registrado exitosamente';
    const INS_SUCCESS = 'Usuario creado exitosamente';
    const UPD_SUCCESS = 'Usuario modificado exitosamente';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="Por favor ingrese un nombre para el usuario")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $primer_apellido;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $segundo_apellido;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Role", inversedBy="user")
     */
    private $roles;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Profesor", mappedBy="user", cascade={"remove"})
     */
    private $profesor;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message="Por favor ingrese una dirección de correo válida")
     */
    private $e_mail;

    /**
     * @Assert\Type(type="App\Entity\Profesor")
     * @Assert\Valid
     */
    private $profesorForm;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Estudiantes", mappedBy="user", cascade={"remove"})
     */
    private $estudiantes;

    /**
     * @Assert\Type(type="App\Entity\Estudiantes")
     * @Assert\Valid
     */

    private $estudiantesForm;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Canton", inversedBy="estudiantes")
     */
    private $canton;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $celular;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre_institucion;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pais_institucion;

    /**
     * @ORM\OneToMany(targetEntity=Notas::class, mappedBy="user", orphanRemoval=true)
     */
    private $notas;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $confirmationToken;

    /**
     * @ORM\OneToMany(targetEntity=ImagenGuardada::class, mappedBy="user", orphanRemoval=true)
     */
    private $imagenGuardadas;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->enabled = false;
        $this->pais_institucion = 'Ecuador';
        $this->nombre_institucion = 'Sistema';
        $this->notas = new ArrayCollection();
        $this->imagenGuardadas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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

    public function getPrimerApellido(): ?string
    {
        return $this->primer_apellido;
    }

    public function setPrimerApellido(string $primer_apellido): self
    {
        $this->primer_apellido = $primer_apellido;

        return $this;
    }

    public function getSegundoApellido(): ?string
    {
        return $this->segundo_apellido;
    }

    public function setSegundoApellido(string $segundo_apellido): self
    {
        $this->segundo_apellido = $segundo_apellido;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEMail()
    {
        return $this->e_mail;
    }

    /**
     * @param mixed $e_mail
     */
    public function setEMail($e_mail): void
    {
        $this->e_mail = $e_mail;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        // guarantee every user at least has ROLE_USER
        if($roles === null){
            $roles = new Role();
            $persistRoles[] = $roles->getRolename();
        } else {
            $persistRoles[] = $roles->getRolename();
        }

        return array_unique($persistRoles);
    }
    public function getRoleObj()
    {
        return $this->roles;
    }

    public function setRoles(Role $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return mixed
     */
    public function getProfesorForm()
    {
        return $this->profesorForm;
    }

    /**
     * @param mixed $profesorForm
     */
    public function setProfesorForm($profesorForm): void
    {
        $this->profesorForm = $profesorForm;
    }

    /**
     * @return mixed
     */
    public function getEstudiantesForm()
    {
        return $this->estudiantesForm;
    }

    /**
     * @param mixed $estudiantesForm
     */
    public function setEstudiantesForm($estudiantesForm): void
    {
        $this->estudiantesForm = $estudiantesForm;
    }

    /**
     * @return mixed
     */
    public function getProfesor()
    {
        return $this->profesor;
    }

    /**
     * @param mixed $profesor
     */
    public function setProfesor($profesor): void
    {
        $this->profesor = $profesor;
    }

    /**
     * @return mixed
     */
    public function getEstudiantes()
    {
        return $this->estudiantes;
    }

    /**
     * @param mixed $estudiantes
     */
    public function setEstudiantes($estudiantes): void
    {

        $this->estudiantes = $estudiantes;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function getPhotoDir(): ?string
    {
        return FileUploader::FOTO_PERFIL.'/'.$this->getUsername().'/'.$this->getPhoto();
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCanton()
    {
        return $this->canton;
    }

    /**
     * @param mixed $canton
     */
    public function setCanton($canton): void
    {
        $this->canton = $canton;
    }

    public function getNombreInstitucion(): ?string
    {
        return $this->nombre_institucion;
    }

    public function setNombreInstitucion(string $nombre_institucion): self
    {
        $this->nombre_institucion = $nombre_institucion;

        return $this;
    }

    public function getPaisInstitucion(): ?string
    {
        return $this->pais_institucion;
    }

    public function setPaisInstitucion(string $pais_institucion): self
    {
        $this->pais_institucion = $pais_institucion;

        return $this;
    }

    /**
     * @return Collection|Notas[]
     */
    public function getNotas(): Collection
    {
        return $this->notas;
    }

    public function addNota(Notas $nota): self
    {
        if (!$this->notas->contains($nota)) {
            $this->notas[] = $nota;
            $nota->setUser($this);
        }

        return $this;
    }

    public function removeNota(Notas $nota): self
    {
        if ($this->notas->contains($nota)) {
            $this->notas->removeElement($nota);
            // set the owning side to null (unless already changed)
            if ($nota->getUser() === $this) {
                $nota->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return string|null
     */
    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    /**
     * @param string|null $confirmationToken
     */
    public function setConfirmationToken(?string $confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }

    /**
     * @return Collection|ImagenGuardada[]
     */
    public function getImagenGuardadas(): Collection
    {
        return $this->imagenGuardadas;
    }

    public function addImagenGuardada(ImagenGuardada $imagenGuardada): self
    {
        if (!$this->imagenGuardadas->contains($imagenGuardada)) {
            $this->imagenGuardadas[] = $imagenGuardada;
            $imagenGuardada->setUser($this);
        }

        return $this;
    }

    public function removeImagenGuardada(ImagenGuardada $imagenGuardada): self
    {
        if ($this->imagenGuardadas->contains($imagenGuardada)) {
            $this->imagenGuardadas->removeElement($imagenGuardada);
            // set the owning side to null (unless already changed)
            if ($imagenGuardada->getUser() === $this) {
                $imagenGuardada->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCelular()
    {
        return $this->celular;
    }

    /**
     * @param mixed $celular
     */
    public function setCelular($celular): void
    {
        $this->celular = $celular;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeInterface $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

}
