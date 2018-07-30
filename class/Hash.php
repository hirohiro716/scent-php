<?php
namespace hirohiro716\Scent;

use Iterator;

/**
 * 連想配列オブジェクトクラス.
 *
 * @author hiro
 */
class Hash extends AbstractObject implements Iterator
{
    
    private $array;
    
    /**
     * コンストラクタ.
     *
     * @param array $array
     */
    public function __construct(array $array = array())
    {
        parent::__construct();
        $this->array = $array;
    }
    
    /**
     * 内部に配列をセットする.
     *
     * @param array $array
     */
    public function setArray(array $array): void
    {
        $this->array = $array;
    }

    /**
     * 内部の配列に対して引数の配列の内容を追加する.
     * キーが重複する場合は上書きされる.
     *
     * @param array $array
     */
    public function addArray(array $array): void
    {
        foreach ($array as $key => $value) {
            $this->array[$key] = $value;
        }
    }

    /**
     * 内部の配列を取得する.
     *
     * @return array
     */
    public function getArray(): array
    {
        return $this->array;
    }

    /**
     * キーと対する値をセットする.
     *
     * @param string $key
     * @param mixed $value
     */
    public function put(string $key, $value): void
    {
        $this->array[$key] = $value;
    }
    
    /**
     * キーを指定せずに値をセットする.
     * 
     * @param mixed $value
     */
    public function add($value): void
    {
        $this->array[] = $value;
    }
    
    /**
     * キーに対する値を取得する.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        if ($this->isExistKey($key)) {
            return $this->array[$key];
        }
        return null;
    }

    /**
     * 保持している値をすべて配列で取得する.
     *
     * @return array
     */
    public function getValues(): array
    {
        return array_values($this->array);
    }

    /**
     * 保持しているキーをすべて配列で取得する.
     *
     * @return array
     */
    public function getKeys(): array
    {
        return array_keys($this->array);
    }

    /**
     * キーが存在するか確認する.
     *
     * @param string $key
     * @return bool
     */
    public function isExistKey($key): bool
    {
        return ArrayHelper::isExistKey($this->array, $key);
    }

    /**
     * 値が存在するかを厳密な型比較を用いて確認する.
     *
     * @param mixed $value
     * @return bool
     */
    public function isExistValue($value): bool
    {
        return ArrayHelper::isExistValue($this->array, $value);
    }

    /**
     * 指定されたキーに対する値を削除する.
     *
     * @param string $key
     */
    public function removeKey($key): void
    {
        if ($this->isExistKey($key)) {
            ArrayHelper::removeKey($this->array, $key);
        }
    }

    /**
     * 指定された値をすべて削除する.
     *
     * @param mixed $value
     */
    public function removeValue($value): void
    {
        ArrayHelper::removeValue($this->array, $value);
    }

    /**
     * 値をすべて削除する.
     */
    public function clear(): void
    {
        $this->array = array();
    }

    /**
     * 保持している値の数を取得する.
     *
     * @return int
     */
    public function size(): int
    {
        return count($this->array);
    }

    /**
     * 保持している値をすべて連結した文字列を取得する.
     *
     * @param string $delimiter
     * @return string
     */
    public function join(string $delimiter): string
    {
        $joinValues = new StringObject();
        foreach ($this->array as $value) {
            if ($joinValues->length() > 0) {
                $joinValues->append($delimiter);
            }
            $joinValues->append($value);
        }
        return $joinValues;
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
     * @return mixed
     */
    public function current()
    {
        $key = $this->getKeys()[$this->position];
        return $this->array[$key];
    }

    /**
     * 現在の要素のキーを返す.
     *
     * @return string
     */
    public function key(): string
    {
        return $this->getKeys()[$this->position];
    }

    /**
     * 次の要素に進む.
     */
    public function next(): void
    {
        $this->position ++;
    }

    /**
     * イテレータの最初の要素に巻き戻す.
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * 現在位置が有効かどうかを調べる.
     *
     * @return bool
     */
    public function valid(): bool
    {
        if ($this->size() <= $this->position) {
            return false;
        }
        $key = $this->getKeys()[$this->position];
        return $this->isExistKey($key);
    }
}