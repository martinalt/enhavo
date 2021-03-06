<?php

namespace Enhavo\Bundle\ShopBundle\DependencyInjection;

use Enhavo\Bundle\AppBundle\Controller\ResourceController;
use Enhavo\Bundle\ShopBundle\Entity\Voucher;
use Enhavo\Bundle\ShopBundle\Factory\VoucherFactory;
use Enhavo\Bundle\ShopBundle\Form\Type\VoucherType;
use Enhavo\Bundle\ShopBundle\Repository\VoucherRepository;
use Enhavo\Bundle\AppBundle\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('enhavo_shop');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue('doctrine/orm')->end()
            ->end()
            
            ->children()
                ->arrayNode('mailer')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('confirm')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('service')->defaultValue('enhavo_shop.mailer.confirm_mailer_default')->end()
                                ->scalarNode('template')->defaultValue('EnhavoShopBundle:Mailer:confirm.html.twig')->end()
                                ->scalarNode('subject')->defaultValue('mailer.confirm.subject')->end()
                                ->scalarNode('translationDomain')->defaultValue('EnhavoShopBundle')->end()
                                ->scalarNode('from')->defaultValue('no-reply@enhavo.com')->end()
                                ->scalarNode('sender_name')->defaultValue('enhavo')->end()
                            ->end()
                        ->end()
                        ->arrayNode('tracking')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('service')->defaultValue('enhavo_shop.mailer.tracking_mailer_default')->end()
                                ->scalarNode('template')->defaultValue('EnhavoShopBundle:Mailer:tracking.html.twig')->end()
                                ->scalarNode('subject')->defaultValue('mailer.tracking.subject')->end()
                                ->scalarNode('translationDomain')->defaultValue('EnhavoShopBundle')->end()
                                ->scalarNode('from')->defaultValue('no-reply@enhavo.com')->end()
                                ->scalarNode('sender_name')->defaultValue('enhavo')->end()
                            ->end()
                        ->end()
                        ->arrayNode('notification')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('notify')->defaultValue(false)->end()
                                ->scalarNode('service')->defaultValue('enhavo_shop.mailer.notification_mailer_default')->end()
                                ->scalarNode('template')->defaultValue('EnhavoShopBundle:Mailer:notification.html.twig')->end()
                                ->scalarNode('subject')->defaultValue('mailer.notification.subject')->end()
                                ->scalarNode('translationDomain')->defaultValue('EnhavoShopBundle')->end()
                                ->scalarNode('from')->defaultValue('no-reply@enhavo.com')->end()
                                ->scalarNode('to')->defaultValue('no-reply@enhavo.com')->end()
                                ->scalarNode('sender_name')->defaultValue('enhavo')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()

            ->children()
                ->arrayNode('document')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('billing')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('generator')->defaultValue('enhavo_shop.document.billing_generator')->end()
                                ->variableNode('options')->end()
                            ->end()
                        ->end()
                        ->arrayNode('packing_slip')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('generator')->defaultValue('enhavo_shop.document.packing_slip_generator')->end()
                                ->variableNode('options')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()

            ->children()
                ->arrayNode('payment')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('paypal')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('logo')->defaultValue(null)->end()
                                ->variableNode('branding')->defaultValue(null)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()

            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('voucher')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Voucher::class)->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->end()
                                        ->scalarNode('repository')->defaultValue(VoucherRepository::class)->end()
                                        ->scalarNode('factory')->defaultValue(VoucherFactory::class)->end()
                                        ->scalarNode('form')->defaultValue(VoucherType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
