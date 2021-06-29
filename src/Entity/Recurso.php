<?php

namespace App\Entity;

use App\Repository\RecursoRepository;
use App\Service\FileUploader;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=RecursoRepository::class)
 * @UniqueEntity(
 *     fields={"nombreRecurso", "tipo", "libro"},
 *     errorPath="nombreRecurso",
 *     message="Existe un Recurso similar asignado a este texto"
 *     )
 */
class Recurso
{
    /**
     * @Groups("recurso")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("recurso")
     * @ORM\Column(type="string", length=255)
     */
    private $nombreRecurso;

    /**
     * @Groups("recurso")
     * @ORM\Column(type="string", length=255)
     */
    private $referencia;

    /**
     * @Groups("recurso")
     * @ORM\ManyToOne(targetEntity=TipoRecurso::class, inversedBy="recursos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tipo;

    /**
     * @Groups("recurso")
     * @ORM\ManyToOne(targetEntity=Libro::class, inversedBy="recursos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $libro;

    /**
     * @Groups("recurso")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mimeType;

    /**
     * @Groups("recurso")
     * @ORM\Column(type="boolean")
     */
    private $paraPlataforma;

    /**
     * @Groups("recurso")
     * @ORM\Column(type="boolean")
     */
    private $paraDocente;

    /**
     * @Groups("recurso")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $referenciaFile;

    /**
     * @Groups("recurso")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $enlace_web;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombreRecurso(): ?string
    {
        return $this->nombreRecurso;
    }

    public function setNombreRecurso(string $nombreRecurso): self
    {
        $this->nombreRecurso = $nombreRecurso;

        return $this;
    }

    public function getReferencia(): ?string
    {
        return $this->referencia;
    }

    public function setReferencia(string $referencia): self
    {
        $this->referencia = $referencia;

        return $this;
    }

    public function getTipo(): ?TipoRecurso
    {
        return $this->tipo;
    }

    public function setTipo(?TipoRecurso $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getLibro(): ?Libro
    {
        return $this->libro;
    }

    public function setLibro(?Libro $libro): self
    {
        $this->libro = $libro;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getParaPlataforma(): ?bool
    {
        return $this->paraPlataforma;
    }

    public function setParaPlataforma(?bool $paraPlataforma): self
    {
        $this->paraPlataforma = $paraPlataforma;

        return $this;
    }

    public function getParaDocente(): ?bool
    {
        return $this->paraDocente;
    }

    public function setParaDocente(?bool $paraDocente): self
    {
        $this->paraDocente = $paraDocente;

        return $this;
    }

    public function getReferenciaFile(): ?string
    {
        return $this->referenciaFile;
    }

    public function setReferenciaFile(?string $referenciaFile): self
    {
        $this->referenciaFile = $referenciaFile;

        return $this;
    }

    public function getReferenciaFileDir(): ?string
    {
        return FileUploader::TEXTOS.'/'.$this->getLibro()->getNombre().'/'.FileUploader::RECURSOS.'/'.$this->getReferenciaFile();
    }

    /**
     * @return mixed
     */
    public function getEnlaceWeb()
    {
        return $this->enlace_web;
    }

    /**
     * @param mixed $enlace_web
     */
    public function setEnlaceWeb($enlace_web): void
    {
        $this->enlace_web = $enlace_web;
    }

}
