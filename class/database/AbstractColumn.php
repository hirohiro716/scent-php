<?php
namespace hirohiro716\Scent\Database;

use Iterator;
use hirohiro716\Scent\ArrayHelper;
use hirohiro716\Scent\StringObject;
use hirohiro716\Scent\AbstractProperty;

/**
 * カラムの抽象クラス。
 * 
 * @author hiro
 */
abstract class AbstractColumn extends AbstractProperty
{
    
    /**
     * テーブル名を取得する。
     * 
     * @return string
     */
    public abstract function getTableName(): string;
    
    /**
     * テーブル名を含むカラムの物理名を取得する。
     * 
     * @return StringObject テーブル名を含むカラム名
     */
    public function getFullPhysicalName(): string
    {
        $name = new StringObject($this->getTableName());
        $name->append(".");
        $name->append($this->getPhysicalName());
        return $name;
    }
    
    /**
     * すべての定数を取得する。
     *
     * @return Columns
     */
    public static function columns(): Columns
    {
        return new Columns(static::values()->toArray());
    }
}

/**
 * ColumnのIterator実装クラス。
 *
 * @author hiro
 */
class Columns implements Iterator
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
     * @return AbstractColumn
     */
    public function current(): AbstractColumn
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
        return ArrayHelper::existsKey($this->values, $this->position);
    }
}