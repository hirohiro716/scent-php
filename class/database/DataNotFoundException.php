<?php
namespace hirohiro716\Scent\Database;

use PDOException;

/**
 * データが存在しない場合の例外クラス.
 *
 * @author hiro
 */
class DataNotFoundException extends PDOException
{
    
    /**
     * データが存在しない場合の例外を作成する。
     * 
     * @param string $message スローする例外メッセージ
     * @param int $code 例外コード
     * @param Throwable 以前に使われた例外（例外の連結に使用する）
     */
    public function __construct($message = null, $code = null, $previous = null)
    {
        $newMessage = $message;
        if ($newMessage === null) {
            $newMessage = "Row is not exist.";
        }
        parent::__construct($newMessage, $code, $previous);
    }

}