<?php
namespace hirohiro716\Scent\Database;

use hirohiro716\Scent\StringObject;
use hirohiro716\Scent\AbstractProperty;

/**
 * カラムの抽象クラス.
 * 
 * @author hiro
 */
abstract class AbstractColumn extends AbstractProperty
{
    
    /**
     * テーブル名を取得する.
     * 
     * @return string
     */
    public abstract function getTableName(): string;
    
    /**
     * テーブル名を含むカラムの物理名を取得する.
     * 
     * @return StringObject テーブル名を含むカラム名
     */
    public function getFullPhysicalName(): StringObject
    {
        $name = new StringObject($this->getTableName());
        $name->append(".");
        $name->append($this->getPhysicalName());
        return $name;
    }
    
}