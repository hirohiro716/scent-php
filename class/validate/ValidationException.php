<?php
namespace hirohiro716\Scent\Validate;

use Exception;

/**
 * 値の検証に失敗した場合の例外クラス。
 *
 * @author hiro
 */
class ValidationException extends Exception
{
    
    /**
     * 値の検証例外を作成する。
     * 
     * @param string $message スローする例外メッセージ
     * @param int $code 例外コード
     * @param Throwable 以前に使われた例外(例外の連結に使用する)
     */
    public function __construct($message = null, $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
}