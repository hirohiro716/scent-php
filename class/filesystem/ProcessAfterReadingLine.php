<?php
namespace hirohiro716\Scent\Filesystem;

/**
 * ファイルの一行を読み込んだ後の処理インターフェース。
 *
 * @author hiro
 */
interface ProcessAfterReadingLine
{
    
    /**
     * ファイルの一行を読み込んだ際に呼び出される。処理を続行する場合はtrueを返す。
     *
     * @param string $line
     * @return bool
     */
    public function call(string $line): bool;
}
