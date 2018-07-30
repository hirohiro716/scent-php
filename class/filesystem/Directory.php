<?php
namespace hirohiro716\Scent\Filesystem;

use ErrorException;
use Iterator;
use hirohiro716\Scent\Hash;
use hirohiro716\Scent\StringObject;

/**
 * Directoryのクラス.
 *
 * @author hiro
 */
class Directory extends AbstractFilesystemItem implements Iterator
{
    
    /**
     * コンストラクタ.
     *
     * @param string $directoryLocation
     */
    public function __construct(string $directoryLocation)
    {
        parent::__construct($directoryLocation);
    }
    
    public function create(): void
    {
        try {
            mkdir($this->getLocation());
        } catch (ErrorException $exception) {
            throw new IOException($this->getAbsoluteLocation(), $exception->getMessage(), $exception->getCode(), $exception);
        }
    }
    
    public function copy(string $destinationLocation): Directory
    {
        $destination = new Directory($destinationLocation);
        if ($destination->isExist() == false) {
            $destination->create();
        }
        foreach ($this as $item) {
            $differPart = new StringObject($item->getAbsoluteLocation());
            $differPart = $differPart->replace($this->getAbsoluteLocation(), "");
            if ($item->isFile()) {
                $item->copy($destination->getAbsoluteLocation() . $differPart);
            }
            if ($item->isDirectory()) {
                $directory = new Directory($destination->getAbsoluteLocation() . $differPart);
                if ($directory->isExist() == false) {
                    $directory->create();
                }
            }
        }
        return $destination;
    }
    
    public function move(string $destinationLocation): Directory
    {
        try {
            $destination = new Directory($destinationLocation);
            rename($this->getAbsoluteLocation(), $destination->getLocation());
            return $destination;
        } catch (ErrorException $exception) {
            throw new IOException($this->getAbsoluteLocation(), $exception->getMessage(), $exception->getCode(), $exception);
        }
    }
    
    public function delete(): void
    {
        try {
            $files = glob($this->getAbsoluteLocation() . "/*");
            foreach ($files as $fileString) {
                $file = new File($fileString);
                if ($file->isFile()) {
                    $file->delete();
                } else {
                    $directory = new Directory($fileString);
                    if ($directory->isDirectory()) {
                        $directory->delete();
                    }
                }
            }
            rmdir($this->getAbsoluteLocation());
        } catch (ErrorException $exception) {
            throw new IOException($this->getAbsoluteLocation(), $exception->getMessage(), $exception->getCode());
        }
    }
    
    public function isExist(): bool
    {
        return file_exists($this->getLocation()) && is_dir($this->getAbsoluteLocation());
    }
    
    public function isReadable(): bool
    {
        return is_readable($this->getAbsoluteLocation()) && is_dir($this->getAbsoluteLocation());
    }
    
    public function isWritable(): bool
    {
        return is_writable($this->getAbsoluteLocation()) && is_dir($this->getAbsoluteLocation());
    }
    
    public function isLink(): bool
    {
        return is_link($this->getAbsoluteLocation()) && is_dir($this->getAbsoluteLocation());
    }
    
    public function getOriginal(): Directory
    {
        if ($this->isLink()) {
            return new Directory(readlink($this->getAbsoluteLocation()));
        }
        return $this;
    }
    
    private $depthOfSubitemSearch = -1;
    
    /**
     * ディレクトリ内のサブアイテムを取得する深度をセットする. 初期値は無制限.
     * 
     * @param int $depth 検索する深度（ゼロで直下のみ）
     */
    public function setDepthOfSubitemSearch(int $depth): void
    {
        $this->depthOfSubitemSearch = $depth;
    }
    
    /**
     * ディレクトリ内のサブアイテムを取得する深度を無制限にする.
     */
    public function unlimitedDepthOfSubitemSearch(): void
    {
        $this->depthOfSubitemSearch = -1;
    }
    
    private $items = null;
    
    private $loadedDepth = null;
    
    /**
     * ディレクトリ内のサブアイテムをすべて取得する.
     * 
     * @return array AbstractFilesystemItemの配列
     */
    public function loadAllSubItems(): array
    {
        $this->prepareSubItems();
        return $this->items->getArray();
    }
    
    /**
     * ディレクトリ内のサブアイテムをすべてインスタンス内に保持する.
     */
    private function prepareSubItems(): void
    {
        if ($this->items === null || $this->depthOfSubitemSearch != $this->loadedDepth) {
            $this->items = new Hash();
            $this->loadSubItems($this->getAbsoluteLocation(), 0);
            $this->loadedDepth = $this->depthOfSubitemSearch;
        }
    }
    
    /**
     * ディレクトリ内のアイテムを再帰的に取得する.
     * 
     * @param string $directoryLocation
     */
    private function loadSubItems(string $directoryLocation, int $depth): void
    {
        $files = glob($directoryLocation . "/*");
        foreach ($files as $fileString) {
            $file = new File($fileString);
            if ($file->isFile()) {
                $this->items->add($file);
            } else {
                $directory = new Directory($fileString);
                if ($directory->isDirectory()) {
                    $this->items->add($directory);
                    if ($this->depthOfSubitemSearch > $depth || $this->depthOfSubitemSearch <= -1) {
                        $this->loadSubItems($file->getAbsoluteLocation(), $depth + 1);
                    }
                }
            }
        }
    }
    
    /*
     * ***********************************
     * ここからIteratorインターフェースの実装.
     * ************************************
     */
    private $position = 0;
    
    /**
     * 現在の要素を返す.
     * 
     * @return AbstractFilesystemItem
     */
    public function current(): AbstractFilesystemItem
    {
        $this->prepareSubItems();
        return $this->items->get($this->position);
    }
    
    /**
     * 現在の要素のキーを返す.
     *
     * @return int
     */
    public function key(): int
    {
        $this->prepareSubItems();
        return $this->position;
    }
    
    /**
     * 次の要素に進む.
     */
    public function next(): void
    {
        $this->prepareSubItems();
        $this->position++;
    }
    
    /**
     * イテレータの最初の要素に巻き戻す.
     */
    public function rewind(): void
    {
        $this->prepareSubItems();
        $this->position = 0;
    }
    
    /**
     * 現在位置が有効かどうかを調べる.
     *
     * @return bool
     */
    public function valid(): bool
    {
        $this->prepareSubItems();
        return $this->items->isExistKey($this->position);
    }
}
