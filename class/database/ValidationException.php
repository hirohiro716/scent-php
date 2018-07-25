<?php
namespace hirohiro716\Scent\Database;

use hirohiro716\Scent;
use hirohiro716\Scent\Hash;

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
     * @param string $message スローする例外メッセージ
     * @param int $code 例外コード
     * @param Throwable 以前に使われた例外（例外の連結に使用する）
     */
    public function __construct($message = null, $code = null, $previous = null)
    {
        $newMessage = $message;
        if ($newMessage === null) {
            $newMessage = "Validation failed of row information.";
        }
        parent::__construct($newMessage, $code, $previous);
    }
    
    private $causeColumns = array();
    
    /**
     * 例外の原因となったカラムを追加する.
     * 
     * @param AbstractColumn $causeColumn
     */
    public function addCauseColumn(ValidationExceptionCauseColumn $causeColumn): void
    {
        $this->causeColumns[] = $causeColumn;
    }
    
    /**
     * 例外の原因となったカラムを取得する.
     * 
     * @return array ValidationExceptionCauseColumnのオブジェクト配列
     */
    public function getCauseColumns(): array
    {
        return $this->causeColumns;
    }
    
    /**
     * 例外の原因となったカラムとメッセージの連想配列を取得する.
     * 
     * @return Hash
     */
    public function toArrayOfCauseMessages(): Hash
    {
        $hash = new Hash();
        foreach ($this->causeColumns as $causeColumn) {
            $hash->put($causeColumn->getColumn()->getPhysicalName(), $causeColumn->getMessage());
        }
        return $hash;
    }
    
}

/**
 * テーブルの行情報の検証に失敗した原因となった列を表すクラス.
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