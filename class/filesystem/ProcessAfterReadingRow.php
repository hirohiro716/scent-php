<?php
namespace hirohiro716\Scent\Filesystem;

use hirohiro716\Scent\Hash;

/**
 * ファイルの行を読み込んだ後の処理インターフェース。
 *
 * @author hiro
 */
interface ProcessAfterReadingRow
{
    
    /**
     * ファイルの行を読み込んだ際に呼び出される。処理を続行する場合はtrueを返す。
     *
     * @param Hash $hash
     * @return bool
     */
    public function call(Hash $hash): bool;
}
