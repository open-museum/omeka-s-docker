<?php
namespace EasyInstall\Service\ControllerPlugin;

use EasyInstall\Mvc\Controller\Plugin\Addons;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AddonsFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedNamed, array $options = null)
    {
        return new Addons(
            $services->get('Omeka\HttpClient')
        );
    }
}
