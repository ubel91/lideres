<?php


namespace App\EventListener;

use App\Entity\Unidad;
use App\Service\FileUploader;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UnidadDeleteFiles
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

    public function postRemove(Unidad $unidad, LifecycleEventArgs $eventArgs)
    {
        try {
            $this->fileUploader->deleteFile(FileUploader::TEXTOS, $unidad->getArchivo(),$unidad->getLibro()->getNombre().'/'.FileUploader::UNIDAD_ARCHIVO, false);
        }catch (\Exception $exception){}
    }

}