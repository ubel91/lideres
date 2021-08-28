<?php

namespace App\Traits;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait BlameableEntityTrait
{
      /**
     * @var User $createdBy
     *
     * @Gedmo\Blameable(on="create")
     * 
     * @ORM\ManyToOne(targetEntity=User::class)
     * 
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    protected $createdBy;

   /**
     * @var User $updatedBy
     *
     * @Gedmo\Blameable(on="update")
     * 
     * @ORM\ManyToOne(targetEntity=User::class)
     * 
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     */
    protected $updatedBy;

    /**
     * @var User $contentChangedBy
     *
     * @Gedmo\Blameable(on="change", field="deleted_at")
     * 
     * @ORM\ManyToOne(targetEntity=User::class)
     * 
     * @ORM\JoinColumn(name="deleted_by", referencedColumnName="id")
     */
    protected $deletedBy;

    public function setCreatedBy(User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setUpdatedBy(User $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    /**
     * Get $contentChangedBy
     *
     * @return  User
     */ 
    public function getDeletedBy()
    {
        return $this->deletedBy;
    }

    /**
     * Set $contentChangedBy
     *
     * @param  User  $deletedBy  $contentChangedBy
     *
     * @return  self
     */ 
    public function setDeletedBy(User $deletedBy)
    {
        $this->deletedBy = $deletedBy;

        return $this;
    }
}
