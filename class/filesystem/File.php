<?php
namespace hirohiro716\Scent\Filesystem;

use ErrorException;
use hirohiro716\Scent\StringObject;

/**
 * Fileのクラス。
 * 
 * @author hiro
 */
class File extends AbstractFilesystemItem
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
    
    public function create(): void
    {
        $this->writeAll("");
    }
    
    public function copy(string $destinationLocation): File
    {
        try {
            $destination = new File($destinationLocation);
            copy($this->getAbsoluteLocation(), $destination->getLocation());
            return $destination;
        } catch (ErrorException $exception) {
            throw new IOException($this->getAbsoluteLocation(), $exception->getMessage(), $exception->getCode());
        }
    }
    
    public function move(string $destinationLocation): File
    {
        try {
            $destination = new File($destinationLocation);
            rename($this->getAbsoluteLocation(), $destination->getLocation());
            return $destination;
        } catch (ErrorException $exception) {
            throw new IOException($this->getAbsoluteLocation(), $exception->getMessage(), $exception->getCode());
        }
    }
    
    public function delete(): void
    {
        try {
            unlink($this->getAbsoluteLocation());
        } catch (ErrorException $exception) {
            throw new IOException($this->getAbsoluteLocation(), $exception->getMessage(), $exception->getCode());
        }
    }
    
    public function exists(): bool
    {
        return file_exists($this->getLocation()) && is_file($this->getAbsoluteLocation());
    }
    
    public function isReadable(): bool
    {
        return is_readable($this->getAbsoluteLocation()) && is_file($this->getAbsoluteLocation());
    }
    
    public function isWritable(): bool
    {
        return is_writable($this->getAbsoluteLocation()) && is_file($this->getAbsoluteLocation());
    }
    
    public function isLink(): bool
    {
        return is_link($this->getAbsoluteLocation()) && is_file($this->getAbsoluteLocation());
    }
    
    public function getOriginal(): File
    {
        if ($this->isLink()) {
            return new File(readlink($this->getAbsoluteLocation()));
        }
        return $this;
    }
    
    /**
     * POSTでアップロードされたファイルか判定する。
     *
     * @return bool
     */
    public function isUploadedFile(): bool
    {
        return is_uploaded_file($this->getAbsoluteLocation());
    }
    
    /**
     * ファイルの内容を読み込む。
     * 
     * @param string $fromEncoding
     * @param string $toEncoding
     * @return string
     * @throws IOException
     */
    public function readAll(string $fromEncoding = null, string $toEncoding = null): string
    {
        return self::readAllContents($this->getAbsoluteLocation(), $fromEncoding, $toEncoding);
    }
    
    /**
     * ファイルの内容を読み込む。
     * 
     * @param ProcessAfterReadingCharacter $processAfterReadingCharacter
     * @param string $fromEncoding
     * @param string $toEncoding
     */
    public function readCharacters(ProcessAfterReadingCharacter $processAfterReadingCharacter, string $fromEncoding = null, string $toEncoding = null): void
    {
        $handle = fopen($this->getAbsoluteLocation(), "r");
        $result = fread($handle, 1);
        while ($result !== false) {
            $character = new StringObject($result);
            if ($processAfterReadingCharacter->call($character->get($fromEncoding, $toEncoding)) == false) {
                break;
            }
            $result = fread($handle, 1);
        }
        if (is_resource($result)) {
            fclose($result);
        }
    }
    
    /**
     * ファイルの内容を読み込む。
     *
     * @param ProcessAfterReadingLine $processAfterReadingLine
     * @param string $fromEncoding
     * @param string $toEncoding
     */
    public function readLines(ProcessAfterReadingLine $processAfterReadingLine, string $fromEncoding = null, string $toEncoding = null): void
    {
        $handle = fopen($this->getAbsoluteLocation(), "r");
        $result = fgets($handle);
        while ($result !== false) {
            $line = new StringObject($result);
            if ($processAfterReadingLine->call($line->get($fromEncoding, $toEncoding)) == false) {
                break;
            }
            $result = fgets($handle);
        }
        if (is_resource($result)) {
            fclose($result);
        }
    }
    
    /**
     * ファイルに文字列を書き込む。
     * 
     * @param string $contents 書き込む内容
     * @return int 書き込まれたバイト数
     * @throws IOException
     */
    public function writeAll(string $contents): int
    {
        try {
            return file_put_contents($this->getLocation(), $contents);
        } catch (ErrorException $exception) {
            throw new IOException($this->getAbsoluteLocation(), $exception->getMessage(), $exception->getCode());
        }
    }
    
    /**
     * ファイルに文字列を書き込む。
     *
     * @param TextWritingProcess $textWritingProcess
     */
    public function write(TextWritingProcess $textWritingProcess): void
    {
        $textWriter = new TextWriter($this);
        $textWritingProcess->call($textWriter);
        $textWriter->close();
    }
    
    /**
     * ファイルを転送してダウンロードさせる。
     * 
     * @param string $filename
     */
    public function transfer(string $filename): void
    {
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=" . $filename);
        while (ob_get_level()) {
            ob_end_flush();
        }
        readfile($this->getAbsoluteLocation());
    }
}
