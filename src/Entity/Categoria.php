<?php

namespace App\Entity;

use App\Repository\CategoriaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CategoriaRepository::class)
 * @UniqueEntity(
 *     fields={"nombre"},
 *     message="Existe una categoria con este nombre"
 *     )
 */
class Categoria
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
     * @ORM\OneToMany(targetEntity="App\Entity\SubCategoria", mappedBy="categoria", cascade={"remove"})
     */
    private $subCategorias;

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
    public function getSubCategorias()
    {
        return $this->subCategorias;
    }

    /**
     * @param mixed $subCategorias
     */
    public function setSubCategorias($subCategorias): void
    {
        $this->subCategorias = $subCategorias;
    }


}
