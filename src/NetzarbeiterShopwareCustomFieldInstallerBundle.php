<?php

declare(strict_types=1);

namespace Netzarbeiter\Shopware\CustomFieldInstaller;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Bundle class
 */
class NetzarbeiterShopwareCustomFieldInstallerBundle extends \Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $this->registerContainerFile($container);
    }

    /**
     * Looks for service definition files inside the `Resources/config` directory and loads either xml or yml files.
     *
     * @see \Shopware\Core\Framework\Bundle::registerContainerFile()
     */
    protected function registerContainerFile(ContainerBuilder $container): void
    {
        $fileLocator = new \Symfony\Component\Config\FileLocator($this->getPath());
        $loaderResolver = new \Symfony\Component\Config\Loader\LoaderResolver([
            new \Symfony\Component\DependencyInjection\Loader\XmlFileLoader($container, $fileLocator),
            new \Symfony\Component\DependencyInjection\Loader\YamlFileLoader($container, $fileLocator),
            new \Symfony\Component\DependencyInjection\Loader\PhpFileLoader($container, $fileLocator),
        ]);
        $delegatingLoader = new \Symfony\Component\Config\Loader\DelegatingLoader($loaderResolver);

        foreach ($this->getServicesFilePathArray($this->getPath() . '/Resources/config/services.*') as $path) {
            $delegatingLoader->load($path);
        }

        if ($container->getParameter('kernel.environment') === 'test') {
            foreach ($this->getServicesFilePathArray($this->getPath() . '/Resources/config/services_test.*') as $testPath) {
                $delegatingLoader->load($testPath);
            }
        }
    }

    /**
     * Find all files matching the given path pattern.
     *
     * @see \Shopware\Core\Framework\Bundle::getServicesFilePathArray()
     *
     * @param string $path
     * @return string[]
     */
    protected function getServicesFilePathArray(string $path): array
    {
        $pathArray = glob($path);

        if ($pathArray === false) {
            return [];
        }

        return $pathArray;
    }
}
