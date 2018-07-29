<?php
namespace hirohiro716\Scent\Filesystem;

use ErrorException;
use hirohiro716\Scent\AbstractObject;
use hirohiro716\Scent\Helper;

/**
 * Filesystemのアイテムを表す抽象クラス.
 * 
 * @author hiro
 */
abstract class AbstractFilesystemItem extends AbstractObject
{
    
    /**
     * コンストラクタ.
     * 
     * @param string $location アイテムの場所
     */
    public function __construct(string $location)
    {
        parent::__construct();
        $this->location = $location;
    }
    
    public function __toString(): string
    {
        return $this->getAbsoluteLocation();
    }
    
    private $location;
    
    /**
     * パスを取得する.
     *
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }
    
    /**
     * 絶対パスを取得する.
     *
     * @return string
     */
    public function getAbsoluteLocation(): string
    {
        return realpath($this->location);
    }
    
    /**
     * ファイルかどうか判定する.
     *
     * @return bool
     */
    public function isFile(): bool
    {
        return is_file($this->getAbsoluteLocation());
    }
    
    /**
     * ファイルオブジェクトに変換する.
     * 
     * @return File
     * @throws IOException
     */
    public function toFile(): File
    {
        if ($this->isFile() == false) {
            throw new IOException($this->getAbsoluteLocation(), "Can't cast to Directory the File instance.");
        }
        return $this;
    }
    
    /**
     * ディレクトリかどうか判定する.
     *
     * @return bool
     */
    public function isDirectory(): bool
    {
        return is_dir($this->getAbsoluteLocation());
    }
    
    /**
     * ディレクトリオブジェクトに変換する.
     * 
     * @return Directory
     * @throws IOException
     */
    public function toDirectory(): Directory
    {
        if ($this->isFile() == false) {
            throw new IOException($this->getAbsoluteLocation(), "Can't cast to File the Directory instance.");
        }
        return $this;
    }
    
    /**
     * 所有者を変更する.
     *
     * @param string|int $user ユーザー名またはユーザー番号
     * @throws IOException
     */
    public function changeOwner($user): void
    {
        try {
            chown($this->getAbsoluteLocation(), $user);
        } catch (ErrorException $exception) {
            throw new IOException($this->getAbsoluteLocation(), $exception->getMessage(), $exception->getCode());
        }
    }
    
    /**
     * 所有グループを変更する.
     *
     * @param string|int $group グループ名またはグループ番号
     * @throws IOException
     */
    public function changeGroup($group): void
    {
        try {
            chgrp($this->getAbsoluteLocation(), $group);
        } catch (ErrorException $exception) {
            throw new IOException($this->getAbsoluteLocation(), $exception->getMessage(), $exception->getCode());
        }
    }
    
    /**
     * モードを変更する.
     *
     * @param string $mode
     * @throws IOException
     */
    public function changeMode($mode): void
    {
        try {
            if (Helper::instanceIsThisName($mode, "string")) {
                $mode8 = intval($mode, 8);
            } else {
                $mode8 = $mode;
            }
            chmod($this->getAbsoluteLocation(), $mode8);
        } catch (ErrorException $exception) {
            throw new IOException($this->getAbsoluteLocation(), $exception->getMessage(), $exception->getCode());
        }
    }
    
    /**
     * 新規作成する.
     * 
     * @throws IOException
     */
    public abstract function create(): void;
    
    /**
     * コピーする.
     *
     * @param string $destination
     * @throws IOException
     */
    public abstract function copy(string $destination);
    
    /**
     * 移動する.
     * 
     * @param string $destination
     * @throws IOException
     */
    public abstract function move(string $destination);
    
    /**
     * 削除する.
     *
     * @throws IOException
     */
    public abstract function delete(): void;
    
    /**
     * 存在するか確認する.
     *
     * @return bool
     */
    public abstract function isExist(): bool;
    
    /**
     * 読み取り可能か判定する.
     *
     * @return bool
     */
    public abstract function isReadable(): bool;
    
    /**
     * 書き込み可能か判定する.
     *
     * @return bool
     */
    public abstract function isWritable(): bool;
    
    /**
     * リンクアイテムか判定する.
     *
     * @return bool
     */
    public abstract function isLink(): bool;
    
    /**
     * リンクの元アイテムを取得する.
     *
     * @return AbstractFilesystemItem
     */
    public abstract function getOriginal();
    
    /**
     * 親ディレクトリを取得する.
     *
     * @return Directory
     */
    public function getParent(): Directory
    {
        return new Directory(dirname($this->getAbsoluteLocation()));
    }
    
    /**
     * ファイルまたはディレクトリが存在するか確認する.
     * 
     * @param string $location
     * @return bool
     */
    public static function isExistItem(string $location): bool
    {
        return file_exists($location);
    }
    
}