<?php

namespace App\Entity;

use App\Repository\CatalogoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CatalogoRepository::class)
 * @UniqueEntity("nombre", message="Existe un catálogo con este nombre")
 */
class Catalogo
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
     * @ORM\OneToMany(targetEntity="App\Entity\Libro", mappedBy="catalogo", cascade={"remove"})
     */
    private $libros;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Etapa", inversedBy="catalogos")
     */
    private $etapas;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Materia", inversedBy="catalogos")
     */
    private $materias;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GradoEscolar", inversedBy="catalogos")
     */
    private $grados;

    /**
     * Catalogo constructor.
     */
    public function __construct()
    {
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

//    /**
//     * @return Collection|Etapa[]
//     */
    public function getEtapas()
    {
        return $this->etapas;
    }

    /**
     * @param mixed $etapas
     */
    public function setEtapas($etapas): void
    {
        $this->etapas = $etapas;
    }

//    /**
//     * @param Etapa $etapa
//     * @return Catalogo
//     */

//    public function setEtapas(Etapa $etapa): self
//    {
//        if (!$this->etapas->contains($etapa)) {
//            $this->etapas[] = $etapa;
//        }
//        return $this;
//    }

//    /**
//     * @return Collection|Materia[]
//     */
    public function getMaterias()
    {
        return $this->materias;
    }

    /**
     * @param mixed $materia
     */
    public function setMaterias($materia): void
    {
            $this->materias = $materia;
    }

    public function removeMateria(Materia $materia): self
    {

        if ($this->materias->contains($materia)) {
            $this->materias->removeElement($materia);
        }
        return $this;
    }

//    /**
//     * @param mixed $materias
//     * @return Catalogo
//     */
//    public function setMaterias(array $materias): self
//    {
//        $this->materias = $materias;
//        return $this;
//    }


//    /**
//     * @return Collection|GradoEscolar[]
//     */
    public function getGrados()
    {
        return $this->grados;
    }

    /**
     * @param mixed $grado
     */
    public function setGrados($grado)
    {
            $this->grados= $grado;
    }

    public function removeGrado(GradoEscolar $grado){

        if ($this->grados->contains($grado)) {
            $this->grados->removeElement($grado);
        }
        return $this;
    }

//    /**
//     * @param mixed $grados
//     * @return Catalogo
//     */
//    public function setGrados(array $grados): self
//    {
//        $this->grados = $grados;
//        return $this;
//    }


}
