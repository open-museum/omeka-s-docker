<?php
namespace EasyInstall\Service\Form;

use EasyInstall\Form\UploadForm;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class UploadFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $form = new UploadForm(null, $options);
        $addons = $services->get('ControllerPluginManager')
            ->get('easyInstallAddons');
        $form->setAddons($addons);
        return $form;
    }
}
