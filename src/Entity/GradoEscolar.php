<?php

namespace App\Entity;

use App\Repository\GradoEscolarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=GradoEscolarRepository::class)
 * @UniqueEntity(
 *     fields={"nombre"},
 *     message="Existe un grado escolar con este nombre."
 * )
 */
class GradoEscolar
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
     * @ORM\OneToMany(targetEntity="App\Entity\Catalogo", mappedBy="grados", cascade={"remove"})
     */
    private $catalogos;

    /**
     * @ORM\OneToMany(targetEntity=Estudiantes::class, mappedBy="grado")
     */
    private $students;


    /**
     * GradoEscolar constructor.
     */
    public function __construct()
    {
        $this->catalogos = new ArrayCollection();
        $this->students = new ArrayCollection();
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
     * @return Collection|Catalogo[]
     */
    public function getCatalogos(): Collection
    {
        return $this->catalogos;
    }

//    /**
//     * @param ArrayCollection $catalogos
//     */
//    public function setCatalogos(ArrayCollection $catalogos): void
//    {
//        $this->catalogos = $catalogos;
//    }

    public function addCatalogo(Catalogo $catalogo)
    {
        if (!$this->catalogos->contains($catalogo)) {
            $this->catalogos[] = $catalogo;
            $catalogo->setGrados($this);
        }
        return $this;
//        $this->catalogos[] = $catalogo;
    }

    public function removeCatalogo(Catalogo $catalogo)
    {
        if ($this->catalogos->contains($catalogo)) {
            $this->catalogos->removeElement($catalogo);
            $catalogo->removeGrado($this);
        }
        return $this;
//        $this->catalogos->removeElement($catalogo);
    }

    /**
     * @return Collection|Estudiantes[]
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Estudiantes $student): self
    {
        if (!$this->students->contains($student)) {
            $this->students[] = $student;
            $student->setGrado($this);
        }

        return $this;
    }

    public function removeStudent(Estudiantes $student): self
    {
        if ($this->students->removeElement($student)) {
            // set the owning side to null (unless already changed)
            if ($student->getGrado() === $this) {
                $student->setGrado(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->nombre;
    }


}
