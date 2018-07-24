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
    public function __construct($message = null, $code = null, $previous = null)
    {
        $newMessage = $message;
        if ($newMessage === null) {
            $newMessage = "Row is not exist.";
        }
        parent::__construct($newMessage, $code, $previous);
    }

}