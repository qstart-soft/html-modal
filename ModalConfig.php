<?php

namespace Qstart\HtmlModal;

use Symfony\Component\Templating\EngineInterface;

/**
 * Configuration class for working with the library `qstart/html-modal`.
 * Usage example:
 *
 * ```php
 *     use Qstart\HtmlModal\ModalConfig;
 *     $config = new ModalConfig($defaultContainer, $templating);
 * ```
 */
class ModalConfig implements ModalConfigInterface
{
    protected $containerRules = [];

    /**
     * @param AbstractModalContainer $defaultContainer Default container
     * @param EngineInterface $templating Engine for working with templates
     * @see https://packagist.org/packages/symfony/templating
     */
    public function __construct(
        protected AbstractModalContainer $defaultContainer,
        protected EngineInterface $templating
    ) {
    }

    /**
     * Set the container to be used by default.
     * If other containers are specified, they are available, but they do not have the required method, then this container will be connected
     *
     * @param AbstractModalContainer $modalContainer
     * @return $this
     */
    public function setDefaultContainer(AbstractModalContainer $modalContainer)
    {
        $this->defaultContainer = $modalContainer;
        return $this;
    }

    /**
     * For the convenience of separating modal windows into different containers and in order not to have to create instances for each container,
     * you must pass an array where:
     * the key is the name of the class that instance of [[AbstractModalContainer]]
     * the value is a function that returns true or false, which means this container is suitable or not.
     *
     * Usage example:
     * ```php
     *     use Qstart\HtmlModal\ModalConfig;
     *
     *     $config = new ModalConfig();
     *     $config->setContainers([
     *         SecondModalContainer::class => fn () => true,
     *     ]);
     * ```
     *
     * @param array $rules Array of the rules
     * @return $this
     */
    public function setContainers(array $rules)
    {
        $this->containerRules = $rules;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultModalContainer(): AbstractModalContainer
    {
        return $this->defaultContainer;
    }

    /**
     * @inheritDoc
     */
    public function getModalContainer(): AbstractModalContainer
    {
        foreach ($this->containerRules as $containerName => $isContainerAllow) {
            $isAllow = false;
            if ($isContainerAllow instanceof \Closure) {
                $isAllow = $isContainerAllow();
            } else {
                $isAllow = !!$isContainerAllow;
            }

            if ($isAllow) {
                return new $containerName();
            }
        }

        return $this->getDefaultModalContainer();
    }

    /**
     * @inheritDoc
     */
    public function getTemplatingEngine(): EngineInterface
    {
        return $this->templating;
    }
}
