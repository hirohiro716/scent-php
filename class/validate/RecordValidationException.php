<?php
namespace hirohiro716\Scent\Validate;

use hirohiro716\Scent\Hash;

/**
 * レコードの検証に失敗した場合の例外クラス。
 * 
 * @author hiro
 */
class RecordValidationException extends PropertyValidationException
{
    
    /**
     * レコードの検証例外を作成する。
     * 
     * @param Hash $causeRecord 例外の原因となったレコード
     * @param string $message スローする例外メッセージ
     * @param int $code 例外コード
     * @param Throwable 以前に使われた例外(例外の連結に使用する)
     */
    public function __construct(Hash $causeRecord, $message = null, $code = null, $previous = null)
    {
        $newMessage = $message;
        if ($newMessage === null) {
            $newMessage = "Validation failed of record information.";
        }
        parent::__construct($newMessage, $code, $previous);
        $this->causeRecord = $causeRecord;
    }
    
    private $causeRecord;
    
    /**
     * 例外の原因となったレコードを取得する。
     * 
     * @return Hash
     */
    public function getCauseRecord(): Hash
    {
        return $this->causeRecord;
    }
}
