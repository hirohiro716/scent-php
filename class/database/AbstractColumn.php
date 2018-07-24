<?php
namespace hirohiro716\Scent\Database;

use hirohiro716\Scent\AbstractEnum;
use hirohiro716\Scent\StringObject;

/**
 * カラムの抽象クラス.
 * 
 * @author hiro
 */
abstract class AbstractColumn extends AbstractEnum
{
    
    /**
     * テーブル名を取得する.
     * 
     * @return string
     */
    public abstract function getTableName(): string;
    
    /**
     * 定数名を小文字にしたカラムの物理名を取得する.
     * 
     * @return StringObject カラム名
     */
    public function getPhysicalName(): StringObject
    {
        return parent::getName()->toLower();
    }
    
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
    
    /**
     * カラムの論理名を取得する.
     * 
     * @return string
     */
    public abstract function getLogicalName(): string;
    
}