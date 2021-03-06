<?php

namespace Enhavo\Bundle\AppBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class EnhavoAppExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $container->setParameter('enhavo_app.stylesheets', $config['stylesheets']);
        $container->setParameter('enhavo_app.javascripts', $config['javascripts']);
        $container->setParameter('enhavo_app.apps', $config['apps']);
        $container->setParameter('enhavo_app.menu', $config['menu']);
        $container->setParameter('enhavo_app.branding', $config['branding']);
        $container->setParameter('enhavo_app.login.route', $config['login']['route']);
        $container->setParameter('enhavo_app.login.route_parameters', $config['login']['route_parameters']);
        $container->setParameter('enhavo_app.template_paths', $config['template_paths']);
        $container->setParameter('enhavo_app.webpack_build', $config['webpack_build']);
        $container->setParameter('enhavo_app.roles', $config['roles']);
        $container->setParameter('enhavo_app.form_themes', $config['form_themes']);
        $container->setParameter('enhavo_app.locale', $config['locale']);
        $container->setParameter('enhavo_app.locale_resolver', $config['locale_resolver']);
        $container->setParameter('enhavo_app.toolbar_widget.primary', $config['toolbar_widget']['primary']);
        $container->setParameter('enhavo_app.toolbar_widget.secondary', $config['toolbar_widget']['secondary']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services/services.yml');
        $loader->load('services/controller.yml');
        $loader->load('services/twig.yml');
        $loader->load('services/init.yml');
        $loader->load('services/locale.yml');
        $loader->load('services/command.yml');
        $loader->load('services/viewer.yml');
        $loader->load('services/filter.yml');
        $loader->load('services/column.yml');
        $loader->load('services/action.yml');
        $loader->load('services/batch.yml');
        $loader->load('services/menu.yml');
        $loader->load('services/chart.yml');
        $loader->load('services/reference.yml');
        $loader->load('services/metadata.yml');
        $loader->load('services/maker.yml');
        $loader->load('services/widget.yml');
        $loader->load('services/toolbar.yml');
    }
}
