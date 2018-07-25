<?php
namespace hirohiro716\Scent\Database;

/**
 * テーブルの行情報の検証に失敗した原因となったカラムを表すクラス.
 * 
 * @author hiro
 */
class ValidationExceptionCauseColumn
{
    
    /**
     * テーブルの行情報の検証例外の原因となったカラムを作成する。
     * 
     * @param AbstractColumn $column 検証失敗の原因となったカラム
     * @param string $message 例外メッセージ
     */
    public function __construct(AbstractColumn $column, string $message)
    {
        $this->column = $column;
        $this->message = $message;
    }
    
    private $column;
    
    /**
     * 例外の原因となったカラムを取得する.
     * 
     * @return AbstractColumn
     */
    public function getColumn(): AbstractColumn
    {
        return $this->column;
    }
    
    private $message;
    
    /**
     * 検証失敗の詳細メッセージを取得する.
     * 
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
    
}