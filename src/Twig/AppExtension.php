<?php


namespace App\Twig;

use App\Entity\Libro;
use App\Entity\User;
use App\Service\FileUploader;
use DateTime;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension implements ServiceSubscriberInterface
{
    private $container;

    private $kernel;

    private $router;

    public function __construct(ContainerInterface $container, KernelInterface $kernel, UrlGeneratorInterface $router)
    {
        $this->container = $container;
        $this->kernel = $kernel;
        $this->router = $router;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('uploaded_asset', [$this, 'getUploadedAssetPath']),
            new TwigFunction('wkpath', [$this, 'getWkPath']),
            new TwigFunction('pypath', [$this, 'getProyectPath']),
            new TwigFunction('bookState', [$this, 'bookState']),
            new TwigFunction('bookState2', [$this, 'bookState2'])
        ];
    }
    public function bookState2(Libro $book, User $user){
        foreach ($user->getCodigos() as $codigo) {
            if ($codigo->getLibro() === $book) {
                if ($codigo->getActivo() && $codigo->getFechaFin() < new DateTime('now')){
                    return '<a href="'. $this->router->generate('codigo_edit', [
                            'id' => $codigo->getId(),
                        ]).'" class="btn btn-warning "><i class="fa fa-exclamation-triangle"></i> Caducado</a>';
                }
                if ($codigo->getActivo() && $codigo->getFechaFin() >= new DateTime('now')){
                    return '<a href="'. $this->router->generate('codigo_edit', [
                            'id' => $codigo->getId(),
                        ]).'" class="btn btn-success"><i class="fa fa-check-circle"></i> Activo</a>';
                }
                return ' <span class="btn btn-danger"><i class="fa fa-times-circle"></i> Desactivado</span>';
            }
        }
        return ' <span class="btn btn-danger"><i class="fa fa-times-circle"></i> Desactivado</span>';
    }

    public function bookState(Libro $book, User $user){
        foreach($book->getLibroActivados() as $activado){
            if($activado->getProfesor() === $user->getProfesor() || $activado->getEstudiante() === $user->getEstudiantes()){
                $codeString = $activado->getCodigoActivacion();
                foreach($book->getCodigos() as $code){
                    if($code->getCodebook() === $codeString){
                        if ($code->getFechaFin() < new DateTime('now')){
                            return '<a href="'. $this->router->generate('codigo_edit', [
                                    'id' => $code->getId(),
                                ]).'" class="btn btn-warning "><i class="fa fa-exclamation-triangle"></i> Caducado</a>';
                        }
                        if ($code->getFechaFin() >= new DateTime('now')){
                            return '<a href="'. $this->router->generate('codigo_edit', [
                                'id' => $code->getId(),
                            ]).'" class="btn btn-success"><i class="fa fa-check-circle"></i> Activo</a>';
                        }
                        return ' <span class="btn btn-danger"><i class="fa fa-times-circle"></i> Desactivado</span>';
                    }
                }
            }
        }
        return ' <span class="btn btn-danger"><i class="fa fa-times-circle"></i> Desactivado</span>';
    }

    public function getUploadedAssetPath(string $path): string
    {
        return $this->container
            ->get(FileUploader::class)
            ->getUploadPath($path);
    }

    public static function getSubscribedServices()
    {
        return [
            FileUploader::class,
        ];
    }

    public function getWkPath($path)
    {
        return $this->kernel->getProjectDir() . '/public' . $path;
    }

    public function getProyectPath($path)
    {
        return $this->kernel->getProjectDir() . $path;
    }

}