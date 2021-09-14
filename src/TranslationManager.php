<?php

declare(strict_types=1);

namespace TranslationLoader;

use TranslationLoader\Data\TranslationGroup;
use TranslationLoader\Reader\TranslationReaderAbstract;
use TranslationLoader\Writer\TranslationWriterAbstract;
use TranslationLoader\Exception\TranslationLoaderException;

class TranslationManager
{
    /** @var array */
    protected $languages = [];

    /**
     * @param array $languages
     * @throws TranslationLoaderException
     */
    public function __construct(array $languages)
    {
        if (empty($languages)) {
            throw new TranslationLoaderException('Languages must be specified!');
        }
        $this->languages = $languages;
    }

    /**
     * @param TranslationReaderAbstract $reader
     * @param TranslationWriterAbstract $writer
     * @return bool
     */
    public function copyTranslations(TranslationReaderAbstract $reader, TranslationWriterAbstract $writer): bool
    {
        foreach ($reader->read() as $translationGroup) {
            /** @var $translationGroup TranslationGroup */
            $writer->write($translationGroup);
        }
        $writer->finalize();
        return true;
    }

    /**
     * @return array
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }
}
