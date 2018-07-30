<?php
namespace hirohiro716\Scent;

/**
 * プロパティの抽象クラス.
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
     * 定数名を小文字にした物理名を取得する.
     *
     * @return StringObject 物理名
     */
    public function getPhysicalName(): StringObject
    {
        return parent::getName()->toLower();
    }
    
    /**
     * 論理名を取得する.
     *
     * @return string 論理名
     */
    public abstract function getLogicalName(): string;
    
}