<?php
namespace hirohiro716\Scent\Validate;

use hirohiro716\Scent\AbstractProperty;

/**
 * プロパティの検証に失敗した原因となったカラムを表すクラス。
 * 
 * @author hiro
 */
class CauseProperty
{
    
    /**
     * プロパティの検証例外の原因となったカラムを作成する。
     * 
     * @param AbstractProperty $property 検証失敗の原因となったカラムやプロパティ
     * @param string $message 例外メッセージ
     */
    public function __construct(AbstractProperty $property, string $message)
    {
        $this->property = $property;
        $this->message = $message;
    }
    
    private $property;
    
    /**
     * 例外の原因となったプロパティを取得する。
     * 
     * @return AbstractProperty
     */
    public function getProperty(): AbstractProperty
    {
        return $this->property;
    }
    
    private $message;
    
    /**
     * 検証失敗の詳細メッセージを取得する。
     * 
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
    
}