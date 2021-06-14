<?php

namespace App\Entity;

use App\Repository\EstudiantesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EstudiantesRepository::class)
 */
class Estudiantes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha_nacimiento;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre_representante;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $primer_apellido_representante;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $segundo_apellido_representante;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $numero_identificacion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $celular;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Identificacion", inversedBy="estudiantes")
     */
    private $identificacion;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="estudiantes")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=LibroActivado::class, mappedBy="estudiante")
     */
    private $libroActivados;

    /**
     * @ORM\ManyToOne(targetEntity=GradoEscolar::class, inversedBy="students")
     */
    private $grado;


    /**
     * Estudiantes constructor.
     */
    public function __construct()
    {
        $this->libroActivados = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaNacimiento(): ?\DateTimeInterface
    {
        return $this->fecha_nacimiento;
    }

    public function setFechaNacimiento(?\DateTimeInterface $fecha_nacimiento): self
    {
        $this->fecha_nacimiento = $fecha_nacimiento;

        return $this;
    }

    public function getNombreRepresentante(): ?string
    {
        return $this->nombre_representante;
    }

    public function setNombreRepresentante(string $nombre_representante): self
    {
        $this->nombre_representante = $nombre_representante;

        return $this;
    }

    public function getPrimerApellidoRepresentante(): ?string
    {
        return $this->primer_apellido_representante;
    }

    public function setPrimerApellidoRepresentante(string $primer_apellido_representante): self
    {
        $this->primer_apellido_representante = $primer_apellido_representante;

        return $this;
    }

    public function getSegundoApellidoRepresentante(): ?string
    {
        return $this->segundo_apellido_representante;
    }

    public function setSegundoApellidoRepresentante(string $segundo_apellido_representante): self
    {
        $this->segundo_apellido_representante = $segundo_apellido_representante;

        return $this;
    }

    public function getNumeroIdentificacion()
    {
        return $this->numero_identificacion;
    }

    public function setNumeroIdentificacion( $numero_identificacion): self
    {
        $this->numero_identificacion = $numero_identificacion;

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


    /**
     * @return Collection|LibroActivado[]
     */
    public function getLibroActivados(): Collection
    {
        return $this->libroActivados;
    }

    public function addLibroActivado(LibroActivado $libroActivado): self
    {
        if (!$this->libroActivados->contains($libroActivado)) {
            $this->libroActivados[] = $libroActivado;
            $libroActivado->setEstudiante($this);
        }

        return $this;
    }

    public function removeLibroActivado(LibroActivado $libroActivado): self
    {
        if ($this->libroActivados->contains($libroActivado)) {
            $this->libroActivados->removeElement($libroActivado);
            // set the owning side to null (unless already changed)
            if ($libroActivado->getEstudiante() === $this) {
                $libroActivado->setEstudiante(null);
            }
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    public function getGrado(): ?GradoEscolar
    {
        return $this->grado;
    }

    public function setGrado(?GradoEscolar $grado): self
    {
        $this->grado = $grado;

        return $this;
    }

}
