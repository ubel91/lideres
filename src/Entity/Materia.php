<?php

namespace App\Entity;

use App\Repository\MateriaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=MateriaRepository::class)
 * @UniqueEntity(
 *     fields={"nombre"},
 *     message="Existe una materia con este nombre."
 * )
 */
class Materia
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
     * @ORM\OneToMany(targetEntity="App\Entity\Catalogo", mappedBy="materias", cascade={"remove"})
     */
    private $catalogos;

    /**
     * Materia constructor.
     */
    public function __construct()
    {
        $this->catalogos = new ArrayCollection();
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
//     * @param mixed $catalogos
//     */
//    public function setCatalogos($catalogos): void
//    {
//        $this->catalogos = $catalogos;
//    }

    public function addCatalogo(Catalogo $catalogo): self
    {
        if (!$this->catalogos->contains($catalogo)) {
            $this->catalogos[] = $catalogo;
            $catalogo->addMaterias($this);
        }

        return $this;
    }
    public function removeCatalogo(Catalogo $catalogo): self
    {
        if ($this->catalogos->contains($catalogo)) {
            $this->catalogos->removeElement($catalogo);
            $catalogo->removeMateria($this);
        }

        return $this;

    }
}
