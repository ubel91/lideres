<?php

namespace App\Entity;

use App\Repository\ActividadesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ActividadesRepository::class)
 * @UniqueEntity(
 *               fields={"pagina","unidad"},
 *               errorPath="pagina",
 *               message="Esta página dispone de una actividad asignada anteriormente")
 *
 */
class Actividades
{
    /**
     * @Groups("actividades")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("actividades")
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @Groups("actividades")
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="integer",
     *     message="El valor debes ser un número entero."
     *     )
     * @Assert\Range(
     *     min=1,
     *     max=1000,
     *     minMessage="El valor debe ser {{ limit }} o mayor",
     *     notInRangeMessage="El valor proporcionado no es válido"
     * )
     * @ORM\Column(type="smallint")
     */
    private $pagina;

    /**
     * @Groups("actividades")
     * 
     * @Assert\Url(
     *     protocols={"https"},
     *     message="La URL, '{{ value }}', no es válida."
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @Groups("actividades")
     * 
     * @Assert\Url(
     *     protocols={"https"},
     *     message="La URL, '{{ value }}', no es válida."
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $soundCloud;

    /**
     * @Groups("actividades")
     * @ORM\ManyToOne(targetEntity=Unidad::class, inversedBy="actividades")
     * @ORM\JoinColumn(nullable=false)
     */
    private $unidad;


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

    public function getPagina(): ?int
    {
        return $this->pagina;
    }

    public function setPagina(int $pagina): self
    {
        $this->pagina = $pagina;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUnidad(): ?Unidad
    {
        return $this->unidad;
    }

    public function setUnidad(?Unidad $unidad): self
    {
        $this->unidad = $unidad;

        return $this;
    }

    /**
     * Get protocols={"https"},
     */ 
    public function getSoundCloud()
    {
        return $this->soundCloud;
    }

    /**
     * Set protocols={"https"},
     *
     * @return  self
     */ 
    public function setSoundCloud($soundCloud)
    {
        $this->soundCloud = $soundCloud;

        return $this;
    }
}
