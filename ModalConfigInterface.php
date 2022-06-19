<?php

namespace Qstart\HtmlModal;

use Symfony\Component\Templating\EngineInterface;

interface ModalConfigInterface
{
    /**
     * The class to be used by default.
     * If the required method is not found in other classes, the search will be performed in this class.
     *
     * To disable search in the class by default, you can use the method [[AbstractModalContainer::isPassThrough()]] in any modal class inherited from [[AbstractModalContainer]]
     *
     *
     * @return AbstractModalContainer
     */
    public function getDefaultModalContainer(): AbstractModalContainer;

    /**
     * The current modal class.
     * This class will search for the required method.
     *
     * @return AbstractModalContainer
     */
    public function getModalContainer(): AbstractModalContainer;

    /**
     * Template Engine.
     *
     * Required to render the modal window.
     *
     * @return EngineInterface
     */
    public function getTemplatingEngine(): EngineInterface;
}
