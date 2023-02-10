<?php


namespace App\EventListener;

use App\Entity\ImagenGuardada;
use App\Service\FileUploader;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class ImagenGuardadaDeleteFiles
{

    private $fileUploader;
    private $tokenStorage;

    /**
     * ImagenGuardadaDeleteFiles constructor.
     * @param FileUploader $fileUploader
     * @param TokenStorage $tokenStorage
     */
    public function __construct(FileUploader $fileUploader, TokenStorage $tokenStorage)
    {
        $this->fileUploader = $fileUploader;
        $this->tokenStorage = $tokenStorage;
    }

    public function postRemove(ImagenGuardada $imagenGuardada, LifecycleEventArgs $eventArgs)
    {
        $username = $this->tokenStorage->getToken()->getUsername();
        $this->fileUploader->deleteFile(FileUploader::FOTO_PERFIL.'/'.$username, $imagenGuardada->getArchivo(), FileUploader::IMAGEN_GUARDADA, false);
    }

}