<?php
namespace hirohiro716\Scent\Database;

use hirohiro716\Scent\Hash;
use hirohiro716\Scent\ValidationException as BaseValidationException;

/**
 * テーブルの行情報の検証に失敗した場合の例外クラス.
 * 
 * @author hiro
 */
class ValidationException extends BaseValidationException
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
        $this->causeColumns = new Hash();
    }
    
    private $causeColumns;
    
    /**
     * 例外の原因となったカラムを追加する.
     * 
     * @param ValidationExceptionCauseColumn $causeColumn
     */
    public function addCauseColumn(ValidationExceptionCauseColumn $causeColumn): void
    {
        $this->causeColumns->add($causeColumn);
    }
    
    /**
     * 例外の原因となったすべてのカラムを取得する.
     * 
     * @return Hash ValidationExceptionCauseColumnの連想配列
     */
    public function getCauseColumns(): Hash
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

