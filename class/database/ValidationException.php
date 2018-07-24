<?php
namespace hirohiro716\Scent\Database;

use hirohiro716\Scent;

/**
 * テーブルの行情報の検証に失敗した場合の例外クラス.
 * 
 * @author hiro
 */
class ValidationException extends Scent\ValidationException
{
    
    /**
     * テーブルの行情報の検証例外を作成する。
     * 
     * @param AbstractColumn $column 例外の原因になったカラム
     * @param string $message スローする例外メッセージ
     * @param int $code 例外コード
     * @param Throwable 以前に使われた例外（例外の連結に使用する）
     */
    public function __construct(AbstractColumn $column, $message = null, $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->column = $column;
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
    
}