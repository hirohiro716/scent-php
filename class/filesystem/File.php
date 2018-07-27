<?php
namespace hirohiro716\Scent\Filesystem;

use hirohiro716\Scent\AbstractObject;
use hirohiro716\Scent\Helper;

/**
 * Fileのクラス.
 * 
 * @author hiro
 */
class File extends AbstractObject
{
    
    private $location;
    
    /**
     * コンストラクタ.
     * 
     * @param string $fileLocation
     */
    public function __construct(string $fileLocation)
    {
        $this->location = $fileLocation;
    }
    
    /**
     * ファイルが読み取り可能か判定する.
     * 
     * @return bool
     */
    public function isReadable(): bool
    {
        return is_readable($this->location) && is_file($this->location);
    }
    
    /**
     * ファイルが書き込み可能か判定する.
     * 
     * @return bool
     */
    public function isWritable(): bool
    {
        return is_writable($this->location) && is_file($this->location);
    }
    
    /**
     * POSTでアップロードされたファイルか判定する.
     * 
     * @return bool
     */
    public function isUploadedFile(): bool
    {
        return is_uploaded_file($this->location);
    }
    
    /**
     * ファイルがリンクファイルか判定する.
     * 
     * @return bool
     */
    public function isLink(): bool
    {
        return is_link($this->location) && is_file($this->location);
    }
    
    /**
     * リンクファイルの元ファイルを取得する.
     * 
     * @return File
     */
    public function getOriginalFile(): File
    {
        if ($this->isLink()) {
            return new File(readlink($this->location));
        }
        return $this;
    }
    
    /**
     * ファイルの所有者を変更する.
     * 
     * @param string|int $user ユーザー名またはユーザー番号
     */
    public function changeOwner($user): void
    {
        $result = chown($this->location, $user);
        
    }
    
    /**
     * ファイルの所有グループを変更する.
     * 
     * @param string|int $group グループ名またはグループ番号
     * @return bool
     */
    public function changeGroup($group): bool
    {
        return chgrp($this->location, $group);
    }
    
    /**
     * ファイルのモードを変更する.
     * 
     * @param string $mode
     * @return bool
     */
    public function changeMode($mode): bool
    {
        if (Helper::instanceIsThisName($mode, "string")) {
            $mode8 = intval($mode, 8);
        } else {
            $mode8 = $mode;
        }
        return chmod($this->location, $mode8);
    }
    
    /**
     * ファイルをコピーする.
     * 
     * @param string $destination
     * @return bool
     */
    public function copy(string $destination): bool
    {
        return copy($this->location, $destination);
    }
    
    /**
     * ファイルを削除する.
     * 
     * @return bool
     */
    public function delete(): bool
    {
        return unlink($this->location);
    }
    
    /**
     * ファイルの親ディレクトリを取得する.
     * 
     * @return Directory
     */
    public function getParentDirectory(): Directory
    {
        return new Directory(dirname($this->location));
    }
    
    /**
     * 
     * 
     * @param string $contents
     * @return int
     */
    public function writeAll(string $contents): int
    {
        
    }
    
    
    
}