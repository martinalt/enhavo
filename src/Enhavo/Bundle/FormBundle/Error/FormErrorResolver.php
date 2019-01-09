<?php
/**
 * FormErrorResolver.php
 *
 * @since 29/11/16
 * @author gseidel
 */

namespace Enhavo\Bundle\FormBundle\Form\Error;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\FormInterface;

class FormErrorResolver {

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    public function getErrors(FormInterface $form)
    {
        return $this->getErrorMessages($form);
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    protected function getErrorMessages(FormInterface $form)
    {
        $errors = array();

        foreach ($form->getErrors() as $key => $error) {
            $errors[] = $error;
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $childErrors = $this->getErrorMessages($child);
                foreach($childErrors as $childError) {
                    $errors[] = $childError;
                }
            }
        }

        return $errors;
    }

    /**
     * @param FormInterface $form
     * @return boolean
     */
    public function hasErrors(FormInterface $form)
    {
        return (boolean)count($this->getErrorMessages($form));
    }

    /**
     * @param FormInterface $form
     * @return boolean
     */
    public function isSubmitted(FormInterface $form)
    {
        return $form->isSubmitted();
    }

    /**
     * @param FormInterface $form
     * @return boolean
     */
    public function isSuccessful(FormInterface $form)
    {
        return $this->isSubmitted($form) && !$this->hasErrors($form);
    }
}