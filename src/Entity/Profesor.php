<?php

namespace App\Entity;

use App\Repository\ProfesorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProfesorRepository::class)
 */
class Profesor
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $numero_identificacion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $celular;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Identificacion", inversedBy="profesores")
     */
    private $identificacion;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="profesor")
     */
    private $user;


    /**
     * @ORM\OneToMany(targetEntity=LibroActivado::class, mappedBy="profesor")
     */
    private $libroActivados;

    public function __construct()
    {
        $this->libroActivados = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroIdentificacion(): ?string
    {
        return $this->numero_identificacion;
    }

    public function setNumeroIdentificacion($numero_identificacion): self
    {
        $this->numero_identificacion = $numero_identificacion;

        return $this;
    }

    public function getCelular(): ?string
    {
        return $this->celular;
    }

    public function setCelular(string $celular): self
    {
        $this->celular = $celular;

        return $this;
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
            $libroActivado->setProfesor($this);
        }

        return $this;
    }

    public function removeLibroActivado(LibroActivado $libroActivado): self
    {
        if ($this->libroActivados->contains($libroActivado)) {
            $this->libroActivados->removeElement($libroActivado);
            // set the owning side to null (unless already changed)
            if ($libroActivado->getProfesor() === $this) {
                $libroActivado->setProfesor(null);
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



}
