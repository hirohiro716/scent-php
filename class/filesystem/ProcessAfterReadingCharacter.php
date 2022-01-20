<?php
namespace hirohiro716\Scent\Filesystem;

/**
 * ファイルの一文字を読み込んだ後の処理インターフェース。
 *
 * @author hiro
 *
 */
interface ProcessAfterReadingCharacter
{
    
    /**
     * ファイルの一文字を読み込んだ際に呼び出される。処理を続行する場合はtrueを返す。
     *
     * @param string $character
     * @return bool
     */
    public function call(string $character): bool;
}
