<?php

declare(strict_types=1);

namespace TranslationLoader\Reader;

use Generator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use TranslationLoader\Data\TranslationGroup;
use TranslationLoader\Data\TranslationItem;
use TranslationLoader\TranslationManager;
use TranslationLoader\Exception\TranslationLoaderException;

class XlsxReader extends TranslationReaderAbstract
{
    /** @var Spreadsheet */
    protected $spreadsheet;

    /**
     * @param TranslationManager $manager
     * @param string $filePath
     * @throws TranslationLoaderException
     */
    public function __construct(TranslationManager $manager, string $filePath)
    {
        parent::__construct($manager);

        if (!is_file($filePath)) {
            throw new TranslationLoaderException("File not found: {$filePath}!");
        }

        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $this->spreadsheet = $reader->load($filePath);
    }

    /**
     * @return Generator
     */
    public function read(): Generator
    {
        $availableCellIDs = ['A' => 'category', 'B' => 'code'];
        foreach ($this->spreadsheet->getActiveSheet()->getRowIterator() as $rowID => $row) {
            if ($rowID == 1) {
                foreach ($row->getCellIterator() as $cellID => $cell) {
                    $cellValue = mb_strtolower(trim($cell->getValue() ?: ''));
                    if (in_array($cellValue, $availableCellIDs)) {
                        continue;
                    } else {
                        if (in_array($cellValue, $this->manager->getLanguages())) {
                            $availableCellIDs[$cellID] = $cellValue;
                        }
                    }
                }
                continue;
            }

            $data = new TranslationGroup();
            foreach ($row->getCellIterator() as $cellID => $cell) {
                if (!isset($availableCellIDs[$cellID])) {
                    continue;
                }

                $cellValue = trim($cell->getValue() ?: '');

                if ($cellID == 'A') {
                    $data->category = $cellValue;
                    continue;
                }
                if ($cellID == 'B') {
                    $data->code = $cellValue;
                    continue;
                }
                $item = new TranslationItem();
                $item->language = $availableCellIDs[$cellID];
                $item->content = $cellValue;
                $data->items[] = $item;
            }

            if (empty($data->category) || empty($data->code)) {
                continue;
            }

            yield $data;
        }
    }
}
