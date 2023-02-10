<?php

namespace App\Entity;

use App\Repository\CodigoRepository;
use App\Traits\BlameableEntityTrait;
use App\Traits\SoftDeleteableEntityTrait;
use App\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Gedmo\SoftDeleteable\Traits\SoftDeleteable;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=CodigoRepository::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=true, hardDelete=true)
 * @ORM\HasLifecycleCallbacks 
 */
class Codigo
{
    use TimestampableTrait;
    use BlameableEntityTrait;
    use SoftDeleteableEntityTrait;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $codebook;

    /**
     * @ORM\ManyToOne(targetEntity=Libro::class, inversedBy="codigos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $libro;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank
     * @Assert\Date
     */
    private $fechaInicio;

    /**
     * @ORM\Column(type="date")
     * @Assert\GreaterThan(propertyPath="fechaInicio")
     * @Assert\Date
     */
    private $fechaFin;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodebook(): ?string
    {
        return $this->codebook;
    }

    public function setCodebook(string $codebook): self
    {
        $this->codebook = $codebook;

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

    public function getFechaInicio(): ?\DateTimeInterface
    {
        return $this->fechaInicio;
    }

    public function setFechaInicio(\DateTimeInterface $fechaInicio): self
    {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    public function getFechaFin(): ?\DateTimeInterface
    {
        return $this->fechaFin;
    }

    public function setFechaFin(\DateTimeInterface $fechaFin): self
    {
        $this->fechaFin = $fechaFin;

        return $this;
    }
}
