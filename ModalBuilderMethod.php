<?php

declare(strict_types=1);

namespace Qstart\HtmlModal;

use Attribute;

/**
 * @Annotation
 * @Target("ALL")
 */
#[Attribute(Attribute::TARGET_ALL)]
final class ModalBuilderMethod
{
    public const EMPTY_BUILDER = 0;

    /** @var string */
    public $methodName;

    public function __construct($methodName)
    {
        $this->methodName = $methodName;
    }
}
