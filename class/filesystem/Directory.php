<?php
namespace hirohiro716\Scent\Filesystem;

use hirohiro716\Scent\AbstractObject;

/**
 * Directoryのクラス.
 *
 * @author hiro
 */
class Directory extends AbstractObject
{
    
    private $location;
    
    /**
     * コンストラクタ.
     *
     * @param string $directoryLocation
     */
    public function __construct(string $directoryLocation)
    {
        $this->location = $directoryLocation;
    }
    
    /**
     * ディレクトリが読み取り可能か判定する.
     *
     * @return bool
     */
    public function isReadable(): bool
    {
        return is_readable($this->location) && is_dir($this->location);
    }
    
    /**
     * ディレクトリが書き込み可能か判定する.
     *
     * @return bool
     */
    public function isWritable(): bool
    {
        return is_writable($this->location) && is_dir($this->location);
    }
    
    /**
     * ディレクトリがリンクファイルか判定する.
     *
     * @return bool
     */
    public function isLink(): bool
    {
        return is_link($this->location) && is_dir($this->location);
    }
    
    /**
     * リンクディレクトリの元ディレクトリを取得する.
     *
     * @return Directory
     */
    public function getOriginalDirectory(): Directory
    {
        if ($this->isLink()) {
            return new Directory(readlink($this->location));
        }
        return $this;
    }
    
    
    
}
