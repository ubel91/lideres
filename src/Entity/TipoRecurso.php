<?php

namespace App\Entity;

use App\Repository\TipoRecursoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TipoRecursoRepository::class)
 * @UniqueEntity(fields={"nombre"}, message="Existe un Tipo de Recursos con esta denominación")
 */
class TipoRecurso
{

    const REFERENCE_URL = 'url';
    const REFERENCE_FILE = 'archivo';


    /**
     * @Groups("tipo")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("tipo")
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @Groups("recursos_list")
     * @ORM\OneToMany(targetEntity=Recurso::class, mappedBy="tipo", orphanRemoval=true)
     */
    private $recursos;

    public function __construct()
    {
        $this->recursos = new ArrayCollection();
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
     * @return Collection|Recurso[]
     */
    public function getRecursos(): Collection
    {
        return $this->recursos;
    }

    public function addRecurso(Recurso $recurso): self
    {
        if (!$this->recursos->contains($recurso)) {
            $this->recursos[] = $recurso;
            $recurso->setTipo($this);
        }

        return $this;
    }

    public function removeRecurso(Recurso $recurso): self
    {
        if ($this->recursos->contains($recurso)) {
            $this->recursos->removeElement($recurso);
            // set the owning side to null (unless already changed)
            if ($recurso->getTipo() === $this) {
                $recurso->setTipo(null);
            }
        }

        return $this;
    }
}
