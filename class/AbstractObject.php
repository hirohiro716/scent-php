<?php
namespace hirohiro716\Scent;

use ErrorException;

/**
 * 本パッケージ内クラスの元となるクラス.
 * 
 * @author hiro
 */
abstract class AbstractObject
{
    
    /**
     * コンストラクタ.
     */
    public function __construct()
    {
        set_error_handler("self::errorHandler");
    }
    
    /**
     * エラーをExceptionに変換するハンドラー.
     * 
     * @param int $severity
     * @param string $message
     * @param string $file
     * @param int $line
     * @throws ErrorException
     */
    public static function errorHandler($severity, $message, $file, $line): void
    {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
    
}