<?php
namespace hirohiro716\Scent\Filesystem;

use ErrorException;
use hirohiro716\Scent\StringObject;

/**
 * Fileのクラス.
 * 
 * @author hiro
 */
class File extends AbstractFilesystemItem
{
    
    /**
     * コンストラクタ.
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
    
    public function isExist(): bool
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
     * POSTでアップロードされたファイルか判定する.
     *
     * @return bool
     */
    public function isUploadedFile(): bool
    {
        return is_uploaded_file($this->getAbsoluteLocation());
    }
    
    /**
     * ファイルの内容を読み込む.
     * 
     * @return string
     * @throws IOException
     */
    public function readAll(string $fromEncoding = null, string $toEncoding = null): string
    {
        try {
            $stringObject = new StringObject(file_get_contents($this->getAbsoluteLocation()));
            return $stringObject->get($fromEncoding, $toEncoding);
        } catch (ErrorException $exception) {
            throw new IOException($this->getAbsoluteLocation(), $exception->getMessage(), $exception->getCode());
        }
    }
    
    /**
     * ファイルに文字列を書き込む.
     * 
     * @param string $contents 書き込む内容
     * @return int 書き込まれたバイト数
     * @throws IOException
     */
    public function writeAll(string $contents): int
    {
        try {
            return file_put_contents($this->getAbsoluteLocation(), $contents);
        } catch (ErrorException $exception) {
            throw new IOException($this->getAbsoluteLocation(), $exception->getMessage(), $exception->getCode());
        }
    }
    
}