<?php

namespace App\Entity;

use App\Repository\ImagenGuardadaRepository;
use App\Service\FileUploader;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ImagenGuardadaRepository::class)
 *
 * @UniqueEntity(
 *               fields={"pagina","unidad"},
 *               errorPath="pagina",
 *               message="Esta página dispone de modificaciones guardadas anteriormente")
 *
 */
class ImagenGuardada
{
    /**
     * @Groups("imagenes_guardadas")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("imagenes_guardadas")
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @Groups("imagenes_guardadas")
     * @ORM\Column(type="string", length=255)
     */
    private $archivo;

    /**
     * @Groups("imagenes_guardadas")
     * @ORM\Column(type="string", length=255)
     */
    private $mimeType;

    /**
     * @Groups("imagenes_guardadas")
     * @ORM\ManyToOne(targetEntity=Unidad::class, inversedBy="imagenGuardadas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $unidad;

    /**
     * @Groups("imagenes_guardadas")
     * @ORM\Column(type="text", length=255)
     *
     * @Assert\NotBlank()
     *
     */
    private $pagina;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="imagenGuardadas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

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

    public function getArchivo(): ?string
    {
        return $this->archivo;
    }

    public function setArchivo(string $archivo): self
    {
        $this->archivo = $archivo;

        return $this;
    }

    public function getArchivoDir($username): ?string
    {
        return FileUploader::FOTO_PERFIL.'/'.$username.'/'.FileUploader::IMAGEN_GUARDADA.'/'.$this->getArchivo();
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }


    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

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

    public function getPagina(): ?string
    {
        return $this->pagina;
    }

    public function setPagina(string $pagina): self
    {
        $this->pagina = $pagina;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
