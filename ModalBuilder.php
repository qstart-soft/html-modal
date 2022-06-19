<?php

namespace Qstart\HtmlModal;

use ReflectionClass;
use ReflectionMethod;

/**
 * A builder class for rendering a modal window. Created from a configuration object.
 *
 * Inside the class, the first visible container is taken from the list of containers and the method is searched for by attribute in it,
 * if the method is not found, then the default container is searched similarly.
 *
 * Usage example:
 * ```php
 *     use Qstart\HtmlModal\ModalConfig;
 *
 *     $config = new ModalConfig($defaultContainer, $templating);
 *     $builder = new ModalBuilder($config, $modalName, $modalId);
 *
 *     $content = $builder->getContent();
 * ```
 */
class ModalBuilder
{
    /**
     * @param ModalConfigInterface $config Instance of configuration class. By default, you can use [[Qstart\HtmlModal\ModalConfig]]
     * @param string $modalName The name of the modal, it will be looked up in the attribute [[Qstart\HtmlModal\ModalName]]
     * @param $modalId
     */
    public function __construct(
        public ModalConfigInterface $config,
        public string $modalName,
        public $modalId = null
    ) {
    }

    /**
     * Get the rendered html content of the modal window, including the wrapper from the builder if the attribute is present
     *
     * @return mixed
     * @throws \ReflectionException
     * @throws NotFoundModalException
     */
    public function getContent()
    {
        $content = $this->tryExecuteMethod($this->config->getModalContainer());
        if ($content === null) {
            $content = $this->tryExecuteMethod($this->config->getDefaultModalContainer());
        }
        return $content;
    }

    /**
     * @param $modalContainer
     * @return mixed
     * @throws \ReflectionException
     * @throws NotFoundModalException
     */
    protected function tryExecuteMethod(AbstractModalContainer $modalContainer)
    {
        $modalContainer->setTemplatingEngine($this->config->getTemplatingEngine());

        $reflection = new ReflectionClass($modalContainer);
        $buildMethod = $this->getBuildMethod($reflection);

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            $attributes = $reflectionMethod->getAttributes(ModalName::class);
            foreach ($attributes as $attribute) {
                /** @var ModalName $modalNameInstance */
                $modalNameInstance = $attribute->newInstance();
                if (in_array($this->modalName, $modalNameInstance->values)) {
                    try {
                        $methodBuilder = $this->getBuildMethod($reflectionMethod);
                        $methodBuilder === ModalBuilderMethod::EMPTY_BUILDER && $buildMethod = null;
                        $buildMethod = $methodBuilder ?? $buildMethod;
                        $message = $reflectionMethod->invoke($modalContainer, $this->modalId, $this->modalName);
                        if ($buildMethod) {
                            $message = $modalContainer->{$buildMethod}($message);
                        }
                        return $message;
                    } catch (\Throwable $exception) {
                        throw new NotFoundModalException($exception->getMessage());
                    }
                }
            }
        }
        return null;
    }

    /**
     * @param $reflection ReflectionMethod|ReflectionClass
     * @return string|null
     */
    protected function getBuildMethod($reflection)
    {
        $buildMethod = null;
        $builderAttributes = $reflection->getAttributes(ModalBuilderMethod::class);
        foreach ($builderAttributes as $builderAttribute) {
            /** @var ModalBuilderMethod $ModalBuilderMethodInstance */
            $ModalBuilderMethodInstance = $builderAttribute->newInstance();
            $buildMethod = $ModalBuilderMethodInstance->methodName;
        }
        if (
            $buildMethod
            && (
                ($reflection instanceof ReflectionClass && $reflection->hasMethod($buildMethod))
                || ($reflection instanceof ReflectionMethod && $reflection->getDeclaringClass()->hasMethod($buildMethod))
            )
        ) {
            return $buildMethod;
        }
        return $buildMethod === ModalBuilderMethod::EMPTY_BUILDER ? $buildMethod : null ;
    }
}
