<?php


namespace App\EventListener;

use App\Entity\Recurso;
use App\Entity\TipoRecurso;
use App\Service\FileUploader;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class RecursoDeleteFiles
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

    public function postRemove(Recurso $recurso, LifecycleEventArgs $eventArgs)
    {
        if ($recurso->getTipo()->getNombre() == TipoRecurso::REFERENCE_FILE)
            $this->fileUploader->deleteFile(FileUploader::TEXTOS, $recurso->getReferenciaFile(), $recurso->getLibro()->getNombre().'/'.FileUploader::RECURSOS, false);
    }

}