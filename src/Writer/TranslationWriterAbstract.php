<?php

declare(strict_types=1);

namespace VerteraDev\TranslationLoader\Writer;

use VerteraDev\TranslationLoader\TranslationManager;
use VerteraDev\TranslationLoader\Data\TranslationGroup;

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
