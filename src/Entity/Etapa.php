<?php

namespace App\Entity;

use App\Repository\EtapaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=EtapaRepository::class)
 * @UniqueEntity(
 *     fields={"nombre"},
 *     message="Existe una etapa con este nombre."
 * )
 */
class Etapa
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
     * @ORM\OneToMany(targetEntity="App\Entity\Catalogo", mappedBy="etapas", cascade={"remove"})
     */
    private $catalogos;

    /**
     * Etapa constructor.
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


//    /**
//     * @return Collection|Catalogo[]
//     */
    public function getCatalogos()
    {
        return $this->catalogos;
    }

    public function setCatalogo(Catalogo $catalogo): self
    {
        if (!$this->catalogos->contains($catalogo)) {
            $this->catalogos[] = $catalogo;
            $catalogo->setEtapas($this);
        }

        return $this;
    }

    public function removeCatalogo(Catalogo $catalogo): self
    {
        if ($this->catalogos->contains($catalogo)) {
            $this->catalogos->removeElement($catalogo);
            $catalogo->removeEtapa($this);
        }

        return $this;

//        $this->catalogos->removeElement($catalogo);
    }

//    /**
//     * @param mixed $catalogos
//     */
//    public function setCatalogos($catalogos): void
//    {
//        $this->catalogos = $catalogos;
//    }

}
