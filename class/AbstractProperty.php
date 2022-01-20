<?php
namespace hirohiro716\Scent;

use Iterator;

/**
 * プロパティの抽象クラス。
 *
 * @author hiro
 */
abstract class AbstractProperty extends AbstractEnum
{
    
    public function __toString(): string
    {
        return $this->getPhysicalName();
    }
    
    /**
     * 定数名を小文字にした物理名を取得する。
     *
     * @return StringObject 物理名
     */
    public function getPhysicalName(): string
    {
        return parent::getName()->toLower();
    }
    
    /**
     * 論理名を取得する。
     *
     * @return string 論理名
     */
    public abstract function getLogicalName(): string;
    
    /**
     * すべての定数を取得する。
     *
     * @return Properties
     */
    public static function properties(): Properties
    {
        return new Properties(static::values()->toArray());
    }
}

/**
 * PropertyのIterator実装クラス。
 *
 * @author hiro
 */
class Properties implements Iterator
{
    
    /**
     * コンストラクタ。
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }
    
    private $values = array();
    
    /**
     * すべての定数を配列で取得する。
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->values;
    }
    
    private $position = 0;
    
    /**
     * 現在の要素を返す。
     * 
     * @return AbstractProperty
     */
    public function current(): AbstractProperty
    {
        return $this->values[$this->position];
    }
    
    /**
     * 現在の要素のキーを返す。
     *
     * @return int
     */
    public function key(): int
    {
        return $this->position;
    }
    
    /**
     * 次の要素に進む。
     */
    public function next(): void
    {
        $this->position++;
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
        return ArrayHelper::isExistKey($this->values, $this->position);
    }
}
