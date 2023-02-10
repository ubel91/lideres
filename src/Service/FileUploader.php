<?php
namespace App\Service;

use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    const TEXTOS = 'textos';
    const PORTADA_IMAGEN = 'portadas';
    const UNIDAD_ARCHIVO = 'unidades';
    const FOTO_PERFIL = 'perfil';
    const RECURSOS = 'recursos';
    const IMAGEN_GUARDADA = 'imagen_guardada';
    const CACHE = 'cache';

    private $targetDirectory;
    private $filesDirectory;
    private $uploadsDirectory;
    private $requestStackContext;
    private $filesystem;
    private $logger;

    public function __construct($targetDirectory, $filesDirectory, $uploadsDirectory, RequestStackContext $requestStackContext, FilesystemInterface $uploadFilesystem, LoggerInterface $logger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->filesDirectory = $filesDirectory;
        $this->uploadsDirectory = $uploadsDirectory;
        $this->requestStackContext = $requestStackContext;
        $this->filesystem = $uploadFilesystem;
        $this->logger = $logger;
    }

    public function upload(UploadedFile $file, string $destinationPic, ?string $existingFilename, ?string $subFolder)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        $message = '';
        try {
                $message = '¡Ha ocurrido un error al cargar la imagen!';
                if ($destinationPic == self::FOTO_PERFIL){
                    $this->uploadHandler($file, $fileName, $existingFilename,self::FOTO_PERFIL, $subFolder);
                }
                elseif ($destinationPic == self::PORTADA_IMAGEN){
                    $this->uploadHandler($file, $fileName, $existingFilename,self::TEXTOS, $subFolder);
                }
                elseif ($destinationPic == self::RECURSOS){
                    $this->uploadHandler($file, $fileName, $existingFilename,self::TEXTOS, $subFolder.'/'.self::RECURSOS);
                    $message = '¡Ha ocurrido un error al cargar el archivo!';
                } elseif($destinationPic == self::UNIDAD_ARCHIVO) {
                    $this->uploadHandler($file, $fileName, $existingFilename,self::TEXTOS, $subFolder.'/'.self::UNIDAD_ARCHIVO);
                    $message = '¡Ha ocurrido un error al cargar el archivo!';
                } elseif($destinationPic == self::IMAGEN_GUARDADA) {
                    $this->uploadHandler($file, $fileName, $existingFilename,self::FOTO_PERFIL, $subFolder.'/'.self::IMAGEN_GUARDADA);
                    $message = '¡Ha ocurrido un error al cargar el archivo!';
                }
        } catch (FileException $e) {
            throw new \Exception($message);
        }

        return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
    public function getFileDirectory()
    {
        return $this->filesDirectory;
    }
    public function getUploadsDirectory()
    {
        return $this->uploadsDirectory;
    }

    private function uploadHandler(UploadedFile $file, $fileName, ?string $existingFilename, $directory, ?string $subFolder) {

        $stream = fopen($file->getPathname(), 'r');
        if ($subFolder){
            $path = '/'.$directory.'/'.$subFolder.'/'.$fileName;
        }
        else{
            $path = '/'.$directory.'/'.$fileName;
        }
        $result = $this->filesystem->writeStream($path, $stream);

        if ($result === false){
            throw new \Exception(sprintf('No se puede salvar el archivo "%s"', $fileName));
        }
        if (is_resource($stream)){
            fclose($stream);
        }
        if ($existingFilename) {
            $this->deleteFile($directory, $existingFilename, $subFolder);
        }
    }

    public function deleteFile(string $destinationPic, ?string $existingFilename, ?string $subFolder, bool $directory = false){
        if ($existingFilename){
            try {
                if ($subFolder)
                    if (!$directory)
                        $path = '/'.$destinationPic.'/'.$subFolder.'/'.$existingFilename;
                    else
                        $path = '/'.$destinationPic.'/'.$subFolder;
                else
                    $path = '/'.$destinationPic.'/'.$existingFilename;

                if (!$directory){
                    $result = $this->filesystem->delete($path);
                } else {
                    $result = $this->filesystem->deleteDir($path);
                }
                if ($result === false){
                    throw new \Exception(sprintf('No se puede borrar el archivo "%s"', $existingFilename));
                }
            } catch (\League\Flysystem\FileNotFoundException $e) {
                $this->logger->alert(sprintf('El antiguo archivo "%s" no se encuentra', $existingFilename));
            }
        }
    }

    public function readStream(string $path)
    {
        $resource = $this->filesystem->readStream('/'.$path);

        if ($resource === false){
            throw new \Exception(sprintf('Fallo al abrir el stream para "%s"', $path));
        }

        return $resource;

    }


//    /**
//     * @param mixed $targetDirectory
//     */
//    public function setTargetDirectory($targetDirectory): void
//    {
//        $this->targetDirectory .= $targetDirectory;
//    }

    public function getUploadPath(string $path): string
    {
        return $this->requestStackContext
                ->getBasePath().'/uploads/'.$path;
    }

    public function getFullUploadPath(string $path): string
    {
        $resource = $this->uploadsDirectory.'/'.$path;
        return $resource;
    }
}
