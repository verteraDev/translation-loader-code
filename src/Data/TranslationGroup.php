<?php

declare(strict_types=1);

namespace VerteraDev\TranslationLoader\Data;

class TranslationGroup
{
    /** @var string */
    public $category;
    /** @var string */
    public $code;
    /** @var TranslationItem[] */
    public $items = [];
}
