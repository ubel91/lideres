<?php

namespace App\Entity;

use App\Repository\IdentificacionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=IdentificacionRepository::class)
 */
class Identificacion
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tipo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Estudiantes", mappedBy="identificacion")
     */
    private $estudiantes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Profesor", mappedBy="identificacion")
     */
    private $profesores;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }
}
