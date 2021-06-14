<?php


namespace App\EventListener;

use App\Entity\Libro;
use App\Service\FileUploader;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class LibroDeleteFiles
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

    public function postRemove(Libro $libro, LifecycleEventArgs $eventArgs)
    {
        $this->fileUploader->deleteFile(FileUploader::TEXTOS, $libro->getPortada(),$libro->getNombre(),true);
    }

}