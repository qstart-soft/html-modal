<?php

declare(strict_types=1);

namespace Qstart\HtmlModal;

use Attribute;

/**
 * @Annotation
 * @Target("METHOD")
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class ModalName
{
    /** @var string[] */
    public $values;

    public function __construct(
        ...$values
    ) {
        $this->values = $values;
    }
}