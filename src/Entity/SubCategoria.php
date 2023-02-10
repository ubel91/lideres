<?php

namespace App\Entity;

use App\Repository\SubCategoriaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=SubCategoriaRepository::class)
 * @UniqueEntity(
 *     fields={"nombre", "categoria"},
 *     errorPath="nombre",
 *     message="Este nombre existe en la Categoría"
 * )
 */
class SubCategoria
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Categoria", inversedBy="subCategorias")
     */
    private $categoria;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Libro", mappedBy="subCategoria", cascade={"remove"})
     */
    private $libros;

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
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * @param mixed $categoria
     */
    public function setCategoria($categoria): void
    {
        $this->categoria = $categoria;
    }



}
