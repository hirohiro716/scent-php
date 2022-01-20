<?php
namespace hirohiro716\Scent\Filesystem;

use hirohiro716\Scent\Hash;
use hirohiro716\Scent\StringObject;

/**
 * CSVファイルのクラス。
 *
 * @author hiro
 */
class CSV extends File
{
    
    /**
     * コンストラクタ。
     *
     * @param string $fileLocation
     */
    public function __construct(string $fileLocation)
    {
        parent::__construct($fileLocation);
    }
    
    private $delimiter = ",";
    
    /**
     * このCSVで使用する値の区切り文字を指定する。初期値はカンマ。
     *
     * @param delimiter
     */
    public function setDelimiter(string $delimiter): void
    {
        $this->delimiter = $delimiter;
    }
    
    private $lineSeparator = "\r\n";
    
    /**
     * このCSVで使用する行の区切り文字を指定する。初期値はCRLF。
     *
     * @param delimiter
     */
    public function setLineSeparator(string $lineSeparator): void
    {
        $this->lineSeparator = $lineSeparator;
    }
    
    /**
     * CSVファイルの内容を読み込む。
     *
     * @param ProcessAfterReadingRow $processAfterReadingCSVRow
     * @param string $fromEncoding
     * @param string $toEncoding
     */
    public function readRows(ProcessAfterReadingRow $processAfterReadingRow, string $fromEncoding = null, string $toEncoding = null): void
    {
        $handle = fopen($this->getAbsoluteLocation(), "r");
        $result = fgetcsv($handle, null, $this->delimiter, "\"", "\\");
        while ($result !== false) {
            $values = new Hash();
            foreach ($result as $value) {
                $valueObject = new StringObject($value);
                $values->add($valueObject->get($fromEncoding, $toEncoding));
            }
            if ($processAfterReadingRow->call($values) == false) {
                break;
            }
            $result = fgetcsv($handle, null, $this->delimiter, "\"", "\\");
        }
        if (is_resource($result)) {
            fclose($result);
        }
    }
    
    /**
     * ファイルに文字列を書き込む
     *
     * @param CSVWritingProcess $csvWritingProcess
     */
    public function writeRows(CSVWritingProcess $csvWritingProcess): void
    {
        $csvWriter = new CSVWriter($this, $this->delimiter, $this->lineSeparator);
        $csvWritingProcess->call($csvWriter);
        $csvWriter->close();
    }
}
