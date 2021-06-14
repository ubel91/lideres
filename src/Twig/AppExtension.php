<?php


namespace App\Twig;

use App\Service\FileUploader;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension implements ServiceSubscriberInterface
{
    private $container;

    private $kernel;

    public function __construct(ContainerInterface $container, KernelInterface $kernel)
    {
        $this->container = $container;
        $this->kernel = $kernel;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('uploaded_asset', [$this, 'getUploadedAssetPath']),
            new TwigFunction('wkpath', [$this, 'getWkPath']),
            new TwigFunction('pypath', [$this, 'getProyectPath'])
        ];
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
        return $this->kernel->getProjectDir()  . $path;
    }

}