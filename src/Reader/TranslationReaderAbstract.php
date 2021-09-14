<?php

declare(strict_types=1);

namespace TranslationLoader\Reader;

use Generator;
use TranslationLoader\TranslationManager;

abstract class TranslationReaderAbstract
{
    /** @var TranslationManager */
    protected $manager;

    public function __construct(TranslationManager $manager)
    {
        $this->manager = $manager;
    }

    abstract public function read(): Generator;
}
