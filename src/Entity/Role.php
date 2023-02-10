<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RoleRepository::class)
 */
class Role
{
    const ROLE_PREFIX = 'ROLE_';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    const ROLE_ESTUDIANTE = 'ROLE_ESTUDIANTE';
    const ROLE_PROFESOR = 'ROLE_PROFESOR';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $rolename;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="roles")
     */
    private $user;

    /**
     * Role constructor.
     */
    public function __construct()
    {
        $this->rolename = 'ROLE_USER';
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRolename(): ?string
    {
        return $this->rolename;
    }

    public function setRolename(string $rolename): self
    {
        $this->rolename = $rolename;

        return $this;
    }
}
