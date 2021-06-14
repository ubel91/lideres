<?php

namespace App\Entity;

use App\Repository\UnidadRepository;
use App\Service\FileUploader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UnidadRepository::class)
 * @UniqueEntity(
 *               fields={"nombre","libro"},
 *               errorPath="nombre",
 *               message="Existe una unidad con el nombre {{ value }} asignada al libro ")
 */
class Unidad
{
    const CREATED_SUCCESS = 'Unidad creada exitosamente';
    /**
     * @Groups("unidad")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("unidad")
     * @Assert\NotBlank(message="Este campo no debe estar vacío")
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @Groups("unidad")
     * @ORM\Column(type="string", length=255)
     */
    private $archivo;

    /**
     * @Groups("unidad")
     * @Assert\NotBlank(message="Este campo no debe estar vacío")
     * @ORM\ManyToOne(targetEntity="App\Entity\Libro", inversedBy="unidades")
     */
    private $libro;

    /**
     * @Groups("unidad")
     * @ORM\Column(type="string", length=255)
     */
    private $nombreArchivo;

    /**
     * @Groups("unidad")
     * @ORM\Column(type="string", length=255)
     */
    private $mimeType;

    /**
     * @Groups("unidad")
     * @ORM\OneToMany(targetEntity=Actividades::class, mappedBy="unidad", orphanRemoval=true, cascade={"persist"})
     *
     */
    private $actividades;

    /**
     * @ORM\OneToMany(targetEntity=Notas::class, mappedBy="unidad", cascade={"remove"})
     */
    private $notas;

    /**
     * @Assert\Type(type="App\Entity\Actividades")
     * @Assert\Valid
     */
    private $actividadForm;

    /**
     * @ORM\OneToMany(targetEntity=ImagenGuardada::class, mappedBy="unidad", cascade={"remove"})
     */
    private $imagenGuardadas;

    public function __construct()
    {
        $this->actividades = new ArrayCollection();
        $this->notas = new ArrayCollection();
        $this->imagenGuardadas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getArchivo(): ?string
    {
        return $this->archivo;
    }

    public function getArchivoDir(): ?string
    {
        return FileUploader::TEXTOS.'/'.$this->getLibro()->getNombre().'/'.FileUploader::UNIDAD_ARCHIVO.'/'.$this->getArchivo();
    }

    public function setArchivo(string $archivo): self
    {
        $this->archivo = $archivo;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLibro()
    {
        return $this->libro;
    }

    /**
     * @param mixed $libro
     */
    public function setLibro($libro): void
    {
        $this->libro = $libro;
    }

    public function getNombreArchivo(): ?string
    {
        return $this->nombreArchivo;
    }

    public function setNombreArchivo(string $nombreArchivo): self
    {
        $this->nombreArchivo = $nombreArchivo;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * @return Collection|Actividades[]
     */
    public function getActividades(): Collection
    {
        return $this->actividades;
    }

    public function addActividade(Actividades $actividade): self
    {
        if (!$this->actividades->contains($actividade)) {
            $this->actividades[] = $actividade;
            $actividade->setUnidad($this);
        }

        return $this;
    }

    public function removeActividade(Actividades $actividade): self
    {
        if ($this->actividades->contains($actividade)) {
            $this->actividades->removeElement($actividade);
            // set the owning side to null (unless already changed)
            if ($actividade->getUnidad() === $this) {
                $actividade->setUnidad(null);
            }
        }

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
            $nota->setUnidad($this);
        }

        return $this;
    }

    public function removeNota(Notas $nota): self
    {
        if ($this->notas->contains($nota)) {
            $this->notas->removeElement($nota);
            // set the owning side to null (unless already changed)
            if ($nota->getUnidad() === $this) {
                $nota->setUnidad(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getActividadForm()
    {
        return $this->actividadForm;
    }

    /**
     * @param mixed $actividadForm
     */
    public function setActividadForm($actividadForm): void
    {
        $this->actividadForm = $actividadForm;
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
            $imagenGuardada->setUnidad($this);
        }

        return $this;
    }

    public function removeImagenGuardada(ImagenGuardada $imagenGuardada): self
    {
        if ($this->imagenGuardadas->contains($imagenGuardada)) {
            $this->imagenGuardadas->removeElement($imagenGuardada);
            // set the owning side to null (unless already changed)
            if ($imagenGuardada->getUnidad() === $this) {
                $imagenGuardada->setUnidad(null);
            }
        }

        return $this;
    }





}
