<?php
namespace hirohiro716\Scent\Filesystem;

/**
 * CSV書き込みの処理インターフェース。
 *
 * @author hiro
 *
 */
interface CSVWritingProcess
{
    
    /**
     * CSVファイルに書き込む際に呼び出される。
     *
     * @param CSVWriter $csvWriter
     */
    public function call(CSVWriter $csvWriter);
}
