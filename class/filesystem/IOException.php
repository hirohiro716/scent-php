<?php
namespace hirohiro716\Scent\Filesystem;

use Exception;

/**
 * ファイルシステムの読み書きの例外クラス.
 * 
 * @author hiro
 */
class IOException extends Exception
{
    
    /**
     * ファイルシステムの読み書きの例外を作成する。
     * 
     * @param string $location 例外発生原因のファイルまたはディレクトリ
     * @param string $message スローする例外メッセージ
     * @param int $code 例外コード
     * @param Throwable 以前に使われた例外（例外の連結に使用する）
     */
    public function __construct(string $location, $message = null, $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->location = $location;
    }
    
    private $location;
    
    /**
     * 例外発生原因のファイルまたはディレクトリを取得する.
     * 
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }
    
}