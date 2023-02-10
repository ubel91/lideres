<?php

namespace App\Entity;

use App\Repository\LibroRepository;
use App\Service\FileUploader;
use App\Traits\BlameableEntityTrait;
use App\Traits\SoftDeleteableEntityTrait;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\SoftDeleteable\Traits\SoftDeleteable;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=LibroRepository::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=true, hardDelete=true)
 * @UniqueEntity(
 *     fields={"nombre"},
 *     message="El texto {{ value }} ha sido registrado anteriormente"
 *     )
 * @ORM\HasLifecycleCallbacks
 */
class Libro
{
    use TimestampableTrait;
    use BlameableEntityTrait;
    use SoftDeleteableEntityTrait;
    /**
     * @Groups("libro")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("libro")
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $nombre;

    /**
     * @Groups("libro")
     * @ORM\ManyToOne(targetEntity="App\Entity\SubCategoria", inversedBy="libros")
     */
    private $subCategoria;

    /**
     * @Groups("libro")
     * @ORM\ManyToOne(targetEntity="App\Entity\Catalogo", inversedBy="libros")
     */
    private $catalogo;

    /**
     * @Groups("libro")
     * @ORM\OneToMany(targetEntity="App\Entity\Unidad", mappedBy="libro", cascade={"remove"})
     */
    private $unidades;

    /**
     * @Groups("libro")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $portada;

    /**
     * @Groups("libro")
     * @ORM\Column(type="boolean")
     */
    private $para_estudiante;

    /**
     * @Groups("libro")
     * @ORM\Column(type="boolean")
     */
    private $para_docente;

    /**
     * @Groups("libro")
     * @ORM\OneToMany(targetEntity=LibroActivado::class, mappedBy="libro", orphanRemoval=true)
     */
    private $libroActivados;

    /**
     * @Groups("libro")
     * @ORM\OneToMany(targetEntity=Codigo::class, mappedBy="libro", orphanRemoval=true)
     */
    private $codigos;

    /**
     * @Groups("recurso_detail")
     * @ORM\OneToMany(targetEntity=Recurso::class, mappedBy="libro", orphanRemoval=true)
     */
    private $recursos;

    /**
     * @Assert\Url(
     *     protocols={"https","http"},
     *     message="La URL, '{{ value }}', no es válida."
     * )
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $book_link;

    /**
     * @ORM\Column(type="boolean")
     */
    private $use_book_link;

    /**
     * @ORM\Column(type="text", name="code", nullable=true)
     */
    private $code;

    /**
     *
     * @ORM\Column(type="text", name="solucionario1", nullable=true)
     */
    private $solucionario;
    /**
     * @ORM\Column(type="boolean", name="use_code")
     */
    private $use_code;

    public function __construct()
    {
        $this->libroActivados = new ArrayCollection();
        $this->codigos = new ArrayCollection();
        $this->recursos = new ArrayCollection();
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


    /**
     * @return \App\Entity\Catalogo
     */
    public function getCatalogo()
    {
        return $this->catalogo;
    }

    /**
     * @param mixed $catalogo
     */
    public function setCatalogo($catalogo): void
    {
        $this->catalogo = $catalogo;
    }

    /**
     * @return mixed
     */
    public function getSubCategoria()
    {
        return $this->subCategoria;
    }

    /**
     * @param mixed $subCategoria
     */
    public function setSubCategoria($subCategoria): void
    {
        $this->subCategoria = $subCategoria;
    }

    public function getPortada(): ?string
    {
        return $this->portada;
    }

    public function getPortadaDir(): ?string
    {
        return FileUploader::TEXTOS.'/'.$this->getNombre().'/'.$this->getPortada();
    }

    public function setPortada(?string $portada): self
    {
        $this->portada = $portada;

        return $this;
    }

    public function getParaEstudiante(): ?bool
    {
        return $this->para_estudiante;
    }

    public function setParaEstudiante(bool $para_estudiante): self
    {
        $this->para_estudiante = $para_estudiante;

        return $this;
    }

    public function getParaDocente(): ?bool
    {
        return $this->para_docente;
    }

    public function setParaDocente(bool $para_docente): self
    {
        $this->para_docente = $para_docente;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUnidades()
    {
        return $this->unidades;
    }

    /**
     * @param mixed $unidades
     */
    public function setUnidades($unidades): void
    {
        $this->unidades = $unidades;
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
            $libroActivado->setLibro($this);
        }

        return $this;
    }

    public function removeLibroActivado(LibroActivado $libroActivado): self
    {
        if ($this->libroActivados->contains($libroActivado)) {
            $this->libroActivados->removeElement($libroActivado);
            // set the owning side to null (unless already changed)
            if ($libroActivado->getLibro() === $this) {
                $libroActivado->setLibro(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Codigo[]
     */
    public function getCodigos(): Collection
    {
        return $this->codigos;
    }

    public function addCodigo(Codigo $codigo): self
    {
        if (!$this->codigos->contains($codigo)) {
            $this->codigos[] = $codigo;
            $codigo->setLibro($this);
        }

        return $this;
    }

    public function removeCodigo(Codigo $codigo): self
    {
        if ($this->codigos->contains($codigo)) {
            $this->codigos->removeElement($codigo);
            // set the owning side to null (unless already changed)
            if ($codigo->getLibro() === $this) {
                $codigo->setLibro(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Recurso[]
     */
    public function getRecursos(): Collection
    {
        return $this->recursos;
    }

    public function addRecurso(Recurso $recurso): self
    {
        if (!$this->recursos->contains($recurso)) {
            $this->recursos[] = $recurso;
            $recurso->setLibro($this);
        }

        return $this;
    }

    public function removeRecurso(Recurso $recurso): self
    {
        if ($this->recursos->contains($recurso)) {
            $this->recursos->removeElement($recurso);
            // set the owning side to null (unless already changed)
            if ($recurso->getLibro() === $this) {
                $recurso->setLibro(null);
            }
        }

        return $this;
    }

    public function getBookLink(): ?string
    {
        return $this->book_link;
    }

    public function setBookLink(?string $book_link): self
    {
        $this->book_link = $book_link;

        return $this;
    }

    public function getUseBookLink(): ?bool
    {
        return $this->use_book_link;
    }

    public function setUseBookLink(bool $use_book_link): self
    {
        $this->use_book_link = $use_book_link;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     * @return Libro
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUseCode()
    {
        return $this->use_code;
    }

    /**
     * @param mixed $use_code
     * @return Libro
     */
    public function setUseCode($use_code)
    {
        $this->use_code = $use_code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSolucionario()
    {
        return $this->solucionario;
    }

    /**
     * @param mixed $solucionario
     */
    public function setSolucionario($solucionario): void
    {
        $this->solucionario = $solucionario;
    }

}
