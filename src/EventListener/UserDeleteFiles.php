<?php


namespace App\EventListener;

use App\Entity\User;
use App\Service\FileUploader;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UserDeleteFiles
{

    private $fileUploader;

    /**
     * UnidadDeleteFiles constructor.
     * @param FileUploader $fileUploader
     */
    public function __construct(FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

    public function postRemove(User $user, LifecycleEventArgs $eventArgs)
    {
        try {
            $this->fileUploader->deleteFile(FileUploader::FOTO_PERFIL, $user->getPhoto(), $user->getUsername(), true);
        }catch (\Exception $exception){}
    }

}