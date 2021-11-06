<?php

declare(strict_types=1);

namespace VerteraDev\TranslationLoader\Reader;

use Generator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use VerteraDev\TranslationLoader\Data\TranslationGroup;
use VerteraDev\TranslationLoader\Data\TranslationItem;
use VerteraDev\TranslationLoader\TranslationManager;
use VerteraDev\TranslationLoader\Exception\TranslationLoaderException;

class XlsxReader extends TranslationReaderAbstract
{
    /** @var string */
    protected $filePath;
    /** @var Spreadsheet|null */
    protected $spreadsheet = null;

    /**
     * @param TranslationManager $manager
     * @param string $filePath
     * @throws TranslationLoaderException
     */
    public function __construct(TranslationManager $manager, string $filePath)
    {
        parent::__construct($manager);

        $this->filePath = $filePath;
    }

    /**
     * @return Generator
     */
    public function read(): Generator
    {
        $availableCellIDs = ['A' => 'category', 'B' => 'code'];
        foreach ($this->getSpreadsheet()->getActiveSheet()->getRowIterator() as $rowID => $row) {
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

    /**
     * @return Spreadsheet|null
     * @throws TranslationLoaderException
     * @throws Exception
     */
    protected function getSpreadsheet()
    {
        if (!is_file($this->filePath)) {
            throw new TranslationLoaderException('File not found!', ['filePath' => $this->filePath]);
        }

        if ($this->spreadsheet === null) {
            $reader = IOFactory::createReaderForFile($this->filePath);
            $reader->setReadDataOnly(true);
            $this->spreadsheet = $reader->load($this->filePath);
        }
        return $this->spreadsheet;
    }
}
