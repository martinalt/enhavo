<?php
/**
 * ItemTypeInterface.php
 *
 * @since 15/03/18
 * @author gseidel
 */

namespace Enhavo\Bundle\FormBundle\DynamicForm;

interface ItemInterface
{
    public function getName();

    public function getLabel();

    public function getType();

    public function getTranslationDomain();
}