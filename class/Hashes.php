<?php
namespace hirohiro716\Scent;

use Iterator;

/**
 * Hashの集合体クラス。
 *
 * @author hiro
 */
class Hashes implements Iterator
{
    
    public function __construct(Hash ...$hashes)
    {
        $this->hashes = new Hash();
        foreach ($hashes as $hash) {
            $this->hashes->add($hash);
        }
    }
    
    private $hashes;
    
    /**
     * すべてのHashを配列で取得する。
     *
     * @return array
     */
    public function toArray(): array
    {
        $array = array();
        foreach ($this->hashes as $hash) {
            $array[] = $hash->getArray();
        }
        return $array;
    }
    
    /**
     * 指定のHashが内部に存在する場合はtrueを返す。
     *
     * @param Hash $hash
     * @return bool
     */
    public function exists(Hash $hash): bool
    {
        return $this->hashes->existsValue($hash);
    }
    
    /**
     * Hashの数を取得する。
     *
     * @return int
     */
    public function size(): int
    {
        return $this->hashes->size();
    }
    
    /**
     * Hashを追加する。
     *
     * @param Hash $hash
     */
    public function add(Hash $hash): void
    {
        $this->hashes->add($hash);
    }
    
    /**
     * 配列をHashとして追加する。
     * 
     * @param array $array
     */
    public function addArray(array ...$arrays): void
    {
        foreach ($arrays as $array) {
            $this->hashes->add(new Hash($array));
        }
    }
    
    /**
     * Hashを取り除く。
     *
     * @param Hash $hash
     */
    public function remove(Hash $hash): void
    {
        $this->hashes->removeValue($hash);
    }
    
    /**
     * すべてのHashをクリアする。
     */
    public function clear(): void
    {
        $this->hashes->clear();
    }
    
    /*
     * ***********************************
     * ここからIteratorインターフェースの実装。
     * ************************************
     */
    private $position = 0;
    
    /**
     * 現在の要素を返す。
     *
     * @return mixed
     */
    public function current(): Hash
    {
        $key = $this->hashes->getKeys()[$this->position];
        return $this->hashes->get($key);
    }
    
    /**
     * 現在の要素のキーを返す。
     *
     * @return string
     */
    public function key(): string
    {
        return $this->hashes->getKeys()[$this->position];
    }
    
    /**
     * 次の要素に進む。
     */
    public function next(): void
    {
        $this->position ++;
    }
    
    /**
     * イテレータの最初の要素に巻き戻す。
     */
    public function rewind(): void
    {
        $this->position = 0;
    }
    
    /**
     * 現在位置が有効な場合はtrueを返す。
     *
     * @return bool
     */
    public function valid(): bool
    {
        if ($this->hashes->size() <= $this->position) {
            return false;
        }
        $key = $this->hashes->getKeys()[$this->position];
        return $this->hashes->existsKey($key);
    }
}
