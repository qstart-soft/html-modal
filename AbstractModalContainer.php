<?php

namespace Qstart\HtmlModal;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;

/**
 * An abstract class that all containers used in [[ModalConfig]]
 *
 */
#[ModalBuilderMethod('buildModal')]
abstract class AbstractModalContainer
{
    /** @var EngineInterface */
    protected $templating;

    /**
     * Whether the given class is passable to the default class.
     * If the required method is not found in the given class, the default class will be searched.
     *
     * @return bool
     */
    public function isPassThrough()
    {
        return true;
    }

    /**
     * Installing the engine for working with templates.
     * By default, the engine installed through the configuration file is used,
     * but you can override it, for example, through the constructor of the container class of the instance of the [[AbstractModalContainer]]
     *
     * @param EngineInterface $templating
     * @return void
     * @see https://packagist.org/packages/symfony/templating
     */
    public function setTemplatingEngine(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    /**
     * By default, no additional templates are used to build a modal window.
     *
     * @param string $message
     * @return string
     */
    public function buildModal(string $message)
    {
        return $message;
    }
}
