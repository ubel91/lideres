<?php

namespace App\Entity;

use App\Repository\CantonRepository;
use App\Entity\Provincia;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CantonRepository::class)
 */
class Canton
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
    private $nombre;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Provincia", inversedBy="canton")
     */
    private $provincia;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Estudiantes", mappedBy="canton")
     */
    private $estudiantes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Profesor", mappedBy="canton")
     */
    private $profesores;

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
     * @return mixed
     */
    public function getProvincia()
    {
        return $this->provincia;
    }

    /**
     * @param mixed $provincia
     */
    public function setProvincia($provincia): void
    {
        $this->provincia = $provincia;
    }


}
