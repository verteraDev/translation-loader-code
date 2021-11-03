<?php

declare(strict_types=1);

namespace VerteraDev\TranslationLoader\Writer;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use VerteraDev\TranslationLoader\TranslationManager;
use VerteraDev\TranslationLoader\Data\TranslationGroup;

class XlsxWriter extends TranslationWriterAbstract
{
    /** @var string */
    protected $filePath;
    /** @var Spreadsheet */
    protected $spreadsheet;
    /** @var IWriter */
    protected $writer;
    /** @var array */
    protected $languageMap = [];

    /** @var int */
    protected $rowIndex = 1;

    /**
     * @param TranslationManager $manager
     * @param string $filePath
     */
    public function __construct(TranslationManager $manager, string $filePath)
    {
        parent::__construct($manager);

        $this->filePath = $filePath;
        $this->spreadsheet = new Spreadsheet();
        $this->writer = IOFactory::createWriter($this->spreadsheet, 'Xlsx');
        foreach (array_values(array_unique($this->manager->getLanguages())) as $index => $language) {
            $this->languageMap[$language] = $index + 3;
        }
    }

    /**
     * @param TranslationGroup $translationGroup
     * @return bool
     */
    public function write(TranslationGroup $translationGroup): bool
    {
        if ($this->rowIndex == 1) {
            $this->writeHeader();
            $this->rowIndex++;
        }

        $this->spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $this->rowIndex, trim($translationGroup->category));
        $this->spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $this->rowIndex, trim($translationGroup->code));

        foreach ($translationGroup->items as $translationItem) {
            $cellID = $this->languageMap[$translationItem->language];
            $content = trim($translationItem->content);
            $this->spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cellID, $this->rowIndex, $content);
        }

        $this->rowIndex++;

        return true;
    }

    public function finalize(): void
    {
        $this->writer->save($this->filePath);
    }

    private function writeHeader(): void
    {
        $data = array_merge(['category', 'code'], $this->manager->getLanguages());
        foreach ($data as $index => $value) {
            $this->spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($index + 1, 1, $value);
        }
    }
}
