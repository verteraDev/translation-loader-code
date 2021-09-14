<?php

declare(strict_types=1);

namespace TranslationLoader\Writer;

use TranslationLoader\TranslationManager;
use TranslationLoader\Data\TranslationGroup;

abstract class TranslationWriterAbstract
{
    /** @var TranslationManager $manager */
    protected $manager;

    public function __construct(TranslationManager $manager)
    {
        $this->manager = $manager;
    }

    abstract public function write(TranslationGroup $translationGroup): bool;

    abstract public function finalize(): void;
}
