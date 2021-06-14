<?php

namespace App\Entity;

use App\Repository\LibroActivadoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Validator\Constraints as CodeAssert;


/**
 * @ORM\Entity(repositoryClass=LibroActivadoRepository::class)
 * @UniqueEntity(
 *     fields={"codigoActivacion", "libro"},
 *     errorPath="codigoActivacion",
 *     message="Este codigo ha sido usado anteriormente en este libro"
 * )
 *
 * @CodeAssert\ExistCode
 *
 */
class LibroActivado
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Libro::class, inversedBy="libroActivados")
     * @ORM\JoinColumn(nullable=false)
     */
    private $libro;

    /**
     * @ORM\ManyToOne(targetEntity=Profesor::class, inversedBy="libroActivados")
     */
    private $profesor;

    /**
     * @ORM\ManyToOne(targetEntity=Estudiantes::class, inversedBy="libroActivados")
     */
    private $estudiante;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $codigoActivacion;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getProfesor(): ?Profesor
    {
        return $this->profesor;
    }

    public function setProfesor(?Profesor $profesor): self
    {
        $this->profesor = $profesor;

        return $this;
    }

    public function getEstudiante(): ?Estudiantes
    {
        return $this->estudiante;
    }

    public function setEstudiante(?Estudiantes $estudiante): self
    {
        $this->estudiante = $estudiante;

        return $this;
    }

    public function getCodigoActivacion(): ?string
    {
        return $this->codigoActivacion;
    }

    public function setCodigoActivacion(string $codigoActivacion): self
    {
        $this->codigoActivacion = $codigoActivacion;

        return $this;
    }
}
