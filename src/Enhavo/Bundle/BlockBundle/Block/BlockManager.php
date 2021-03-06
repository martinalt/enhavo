<?php
/**
 * Created by PhpStorm.
 * User: gseidel
 * Date: 23.08.18
 * Time: 21:56
 */

namespace Enhavo\Bundle\BlockBundle\Block;

use Enhavo\Bundle\AppBundle\Exception\ResolverException;
use Enhavo\Bundle\AppBundle\Type\TypeCollector;
use Enhavo\Bundle\AppBundle\Util\DoctrineReferenceFinder;
use Enhavo\Bundle\BlockBundle\Factory\AbstractBlockFactory;
use Enhavo\Bundle\BlockBundle\Model\BlockInterface;
use Enhavo\Bundle\BlockBundle\Model\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BlockManager
{
    use ContainerAwareTrait;

    /**
     * @var DoctrineReferenceFinder
     */
    private $doctrineReferenceFinder;

    /**
     * @var Block[]
     */
    private $blocks = [];

    public function __construct(TypeCollector $collector, DoctrineReferenceFinder $doctrineReferenceFinder, $configurations)
    {
        $this->doctrineReferenceFinder = $doctrineReferenceFinder;

        foreach($configurations as $name => $options) {
            /** @var BlockTypeInterface $configuration */
            $configuration = $collector->getType($options['type']);
            unset($options['type']);
            $block = new Block($configuration, $name, $options);
            $this->blocks[$name] = $block;
        }
    }

    public function getBlocks()
    {
        return $this->blocks;
    }

    public function getBlock($name)
    {
        return $this->blocks[$name];
    }

    public function createViewData(NodeInterface $node, $resource = null)
    {
        /** @var BlockManager $manager */
        $manager = $this;
        $this->walk($node, function (NodeInterface $node) use ($manager, $resource) {
            if($node->getType() === NodeInterface::TYPE_BLOCK) {
                $node->setResource($resource);
                $viewData = $manager->getBlock($node->getName())->createViewData($node->getBlock(), $resource);
                $node->setViewData($viewData);
            }
        });

        $this->walk($node, function (NodeInterface $node) use ($manager, $resource) {
            if($node->getType() === NodeInterface::TYPE_BLOCK) {
                $viewData = $manager->getBlock($node->getName())->finishViewData($node->getBlock(), $node->getViewData(), $resource);
                $node->setViewData($viewData);
            }
        });
    }

    private function walk(NodeInterface $node, $callback)
    {
        $callback($node);
        foreach($node->getChildren() as $child) {
            $this->walk($child, $callback);
        }
    }

    public function getFactory($name)
    {
        $block = $this->getBlock($name);
        $factoryClass = $block->getFactory();
        if($factoryClass) {
            if ($this->container->has($factoryClass)) {
                $factory = $this->container->get($factoryClass);
            } else {
                /** @var AbstractBlockFactory $factory */
                $factory = new $factoryClass($block->getModel());
                if($factory instanceof ContainerInterface) {
                    $factory->setContainer($this->container);
                }
            }
            return $factory;
        }
        return null;
    }

    /**
     * Find the resource that references the block tree that contains the given block
     *
     * @param BlockInterface $block
     * @return object|null
     */
    public function findRootResource(BlockInterface $block)
    {
        if (!$block->getNode()) {
            return null;
        }
        $rootNode = $block->getNode()->getRoot();
        if (!$rootNode) {
            return null;
        }
        $references = $this->doctrineReferenceFinder->findReferencesTo($rootNode, NodeInterface::class, [NodeInterface::class, BlockInterface::class]);
        if (count($references) > 0) {
            return $references[0];
        }
        return null;
    }
}
